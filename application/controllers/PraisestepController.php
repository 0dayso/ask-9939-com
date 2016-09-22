<?php

Zend_Loader::loadClass('Praisestep',MODELS_PATH);

class PraisestepController extends Zend_Controller_Action
{

	private $praisestepObj = '';


	public function init() { 
		$this->ViewObj = Zend_Registry::get('view');
		$this->praisestepObj = new Praisestep();
		parent::init();
	}



	public function praiseAction() {
		try {
		  
            $arrParam=$this->getRequest()->getParams();
            if($arrParam['article']){
                if($arrParam['userId']=='null'){
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $praises['ip']=$ip;
                    $praises['addtime']=time();
                    $praises['mark']='1';
                    $praises['tid']=$arrParam['article'];
                    $stime=time()-3600;
                    $this->praisestepObj->del_time($stime);
                    $where=" ip='".$ip."' and tid='".$praises['tid']."'";
                    $result=$this->praisestepObj->GetCount($where);
                    if($result){
                        return;
                    }else{
                        $this->praisestepObj->add_ip($praises);
                    }
                }
                $praise['tuserid']=$arrParam['answerUser'];
                $praise['puserid']=$arrParam['userId'];
                $praise['tid']=$arrParam['article'];
                $praise['praise']=$arrParam['ding'];
                $praise['step']=$arrParam['cai'];
                $praise['addtime']=time();
                $praise['mark']='1';
                $this->praisestepObj->add($praise);
            }
		}
		catch(Exception $e)	{
			echo $e->getMessage();
		}
	}
}
?>
