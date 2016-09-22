<?php

/**
 * 热门科室、热门部位 处理类
 * @author gaoqing
 */
class HotDepPart extends QModels_Disease_Table
{

    /**
     * 得到 热门科室、热门部位 数据集
     * @author gaoqing
     * @date 2016-04-13
     * @param int $columns 在页面中，显示的列数
     * @return array 热门科室、热门部位 数据集
     */
    public  function getCommonDisDep($columns){
        $cache_key = 'cache_hot_disease_department';
        $cache_data = QLib_Cache_Client::getCache('pages/rand_disease_depart', $cache_key);
        if($cache_data){
            return $cache_data;
        }else{
            $commonDisDep = array();
            //查询科室下的疾病
            $departmentDis = $this->getDepartmentDis($columns);

            //查询部位下症状
            $partSymptom = $this->getPartSymptom($columns);
            if (isset($departmentDis) && !empty($departmentDis)){
                $commonDisDep['departmentDis'] = $departmentDis;
            }
            if (isset($partSymptom) && !empty($partSymptom)){
                $commonDisDep['partSymptom'] = $partSymptom;
            }
            //将查询出的数据，添加到缓存文件中
            
            if (!empty($commonDisDep)){
               QLib_Cache_Client::setCache('pages/rand_disease_depart',$cache_key,$commonDisDep,24);
            }
            return $commonDisDep;
        }
        
    }

    /**
     * 查询科室下的对应疾病集
     * @author gaoqing
     * @date 2016-04-08
     * @param int $columns 在页面中，显示的列数
     * @return array 科室下的对应疾病集
     */
    private  function getDepartmentDis($columns)
    {
        $departmentDis = array();

        //get the first level departments
        $firstLevel = $this->getDepartmentLevel1();
        if (self::isNotNull($firstLevel)) {
            foreach ($firstLevel as $level1) {

                //select diseases that all belong current first level department
                $inner = array();
                $inner['department'] = $level1;
                $size = count($firstLevel) * $columns;
                $inner['disease'] = $this->getDiseaseByDepartment($level1['id'], 'class_level1', 0, $size);
                $departmentDis[] = $inner;
            }
            return $departmentDis;
        }
        return $departmentDis;
    }

    /**
     * 根据部位获取症状
     * @author gaoqing
     * @date 2016-04-13
     * @param int $columns 在页面中，显示的列数
     * @return array 部位及症状集
     */
    private  function getPartSymptom($columns){
        $partSymptom = array();

        $firstLevel = $this->getPartLevel1();
        if (self::isNotNull($firstLevel)){
            foreach ($firstLevel as $level1){
                $inner = array();
                $inner['part'] = $level1;
                $size = count($firstLevel) * $columns;
                $inner['symptom'] = $this->getSymptomsByPartid($level1['id'], 'part_level1', 0, $size);
                $partSymptom[] = $inner;
            }
        }
        return $partSymptom;
    }

    /**
     * 参数不为空的判断
     * @author gaoqing
     * @date 2016-03-25
     * @param mixed $param 参数
     * @return boolean true: 不为空；false: 为空
     */
    private  function isNotNull($param){
        $isNotNull = false;
        if (isset($param) && !empty($param)){
            $isNotNull = true;
        }
        return $isNotNull;
    }

    /**
     * 得到所有的一级科室
     * @author gaoqing
     * @date 2016-04-26
     * @return array 所有的一级科室
     */
    private  function getDepartmentLevel1(){
        $sql = " SELECT id, name, pinyin FROM 9939_department WHERE level = 1 ORDER BY listorder asc ";
        return $this->_db->fetchAll($sql);
    }

    /**
     * 得到所有的一级部位
     * @author gaoqing
     * @date 2016-04-26
     * @return array 所有的一级部位
     */
    private  function getPartLevel1(){
        $sql = " SELECT id, name, pinyin FROM 9939_part WHERE level = 1 ORDER BY listorder asc";
        return $this->_db->fetchAll($sql);
    }

    /**
     * 根据科室，查询科室下的疾病
     * @author gaoqing
     * @date 2016-04-26
     * @param int $level1 一级科室id
     * @param string $class_level 科室级别名称（class_level1 或者 class_level2）
     * @param int $offset 查询起始位置
     * @param int $size 查询数量
     * @return array 科室下的疾病集
     */
    private function getDiseaseByDepartment($level1, $class_level, $offset, $size){
        $order = $this->getRandOrder();

        $sql = " SELECT dsm.id, dsm.name, dsm.pinyin, dsm.pinyin_initial ";
        $sql .= " FROM `9939_depart_rel_merge` drm, `9939_disease_symptom_merge` dsm";
        $sql .= " WHERE drm.unique_key = dsm.unique_key AND drm.source_flag = 1 AND drm.${class_level} = ${level1} ORDER BY ${order} LIMIT ${offset}, ${size}";
        return $this->_db->fetchAll($sql);
    }

    /**
     * 根据部位，查询部位下的症状
     * @author gaoqing
     * @date 2016-04-26
     * @param int $part1 一级部位id
     * @param string $part_level 部位级别名称（part_level1 或者 part_level2）
     * @param int $offset 查询起始位置
     * @param int $size 查询数量
     * @return array 部位下的症状集
     */
    private function getSymptomsByPartid($part1, $part_level, $offset, $size){
        $order = $this->getRandOrder();

        $sql = " SELECT dsm.id, dsm.name, dsm.pinyin, dsm.pinyin_initial ";
        $sql .= " FROM `9939_part_rel_merge` prm, `9939_disease_symptom_merge` dsm";
        $sql .= " WHERE prm.unique_key = dsm.unique_key AND prm.source_flag = 2 AND prm.${part_level} = ${part1} ORDER BY ${order} LIMIT ${offset}, ${size}";
        return $this->_db->fetchAll($sql);
    }

    /**
     * 得到随机的排序字段
     * @author gaoqing
     * @date 2016-04-28
     * @return string 随机的排序字段
     */
    private function getRandOrder(){
        $order = 'id asc';

        $orderColumns = array('dsm.unique_key', 'dsm.name', 'dsm.pinyin', 'dsm.pinyin_initial', 'dsm.capital_sn');
        $randOrderColumnsIndex = rand(0, 4);

        $orderType = array('asc', 'desc');
        $randOrderTypeIndex = rand(0, 1);

        $order = $orderColumns[$randOrderColumnsIndex] . ' ' . $orderType[$randOrderTypeIndex];
        return $order;
    }


}