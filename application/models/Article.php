<?php
class Article extends QModels_Article_Table {
    
    
    public function List_ArticleByIds($articleids=array()){
        if(count($articleids)==0){
            return false;
        }
        $ids = implode(',', $articleids);
        $sql = "select articleid as id,title,url,description,inputtime from article where articleid in ($ids) order by articleid desc";
        $result = $this->db_v2_read->fetchAll($sql);
        return $result;
    }
    
    public function List_DiseaseArticleByIds($articleids=array()) {
        if(count($articleids)==0){
            return false;
        }
        $ids = implode(',', $articleids);
        $sql = "select id,title,url,description,inputtime from 9939_disease_article where id in ($ids) order by id desc";
        $result = $this->db_dzjb_read->fetchAll($sql);
        return $result;
    }

}
