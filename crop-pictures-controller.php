 <?php 
class CropPicturesController extends Controller
{
    

   
    public function filters() {
        return array('accessControl');
    }

    public function accessRules() {
        // allow only authenticated users
        return array(array('allow', //
                'users' => array('@'), //
                ), //
                array('deny', //
                'users' => array('*'), //
                ), //
        );
    }
    
    public function scripts() {
        // Return an array with js files to include.
        return array('/js/croppictures.js', '/js/jquery.carouFredSel-6.1.0-packed.js');
    }
    
    
    public function actionIndex()
    {
//        Register scripts 
        Yii::import('system.web.helpers.CJavascript');
        Yii::app()->clientScript->scriptMap = array(
            'jquery.js' => false,
            'jquery.min.js' => false,
        );
        foreach ($this->scripts() as $script) {
            Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . $script);
        }
        
//      init the view and find the first values to fill carousell and filters
        $selected_crop = '';
        $selected_picture = '';
        $selected_collection = '';
        $last_collection = array();
        $to = date('Y/m/d',mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
        $from = date("Y/m/d", mktime(0, 0, 0, date("m")-3, date("d"), date("Y")));
        $cropPictures = CropPictures::model();
        $available_crops = $cropPictures->getUserCrops(Yii::app()->user->id);
        $selected_crop = $this->get_first($selected_crop, $available_crops)->CropName;
        
        $collections = $cropPictures->getCropCollections($selected_crop, $from, $to);
        if(count($collections)>0){
            $last_collection = $cropPictures->getLastCropCollection($selected_crop);           
            $selected_picture = $this->get_first($selected_picture, $last_collection)->Id;
            $selected_collection = $last_collection[0]->SequenceDate;
        }
        $this->render('index',['crops'=>$available_crops, 'selected_crop'=>$selected_crop, 'selected_collection'=>$selected_collection,
            'collections'=>$collections, 'date_from'=>$from,'date_to'=>$to, 'pictures'=>$last_collection,
            'selected_picture'=>$selected_picture]);
    }
    
//    function to update the collection dropdown filter
    public function actionLoadCollections($cropName, $dateFrom, $dateTo) {
        $collections = CropPictures::model()->getCropCollections($cropName, $dateFrom, $dateTo);
        echo CHtml::tag('option', array('value' => ''), 'Collections:', true);
        foreach ($collections as $collection) {
                echo CHtml::tag('option', array('value' => $collection->SequenceDate), CHtml::encode($collection->SequenceDate), true);
        }   
    }
    
//    function to change the contents of carousel when a new colelction is chosen from the filter
    public function actionUpdatePictures($collection_date) {
        $selected_picture = '';
        $pictures = CropPictures::model()->getBySequenceDate($collection_date);
        $selected_picture = $this->get_first($selected_picture, $pictures)->Id;
//        $this->renderPartial('picture_slider', array('pictures' => $pictures, 'selected_picture'=>$selected_picture)); 
        foreach($pictures as $picture):
            echo '<div rel="stylesheet" href="'.Yii::app()->request->baseUrl."/css/crop-pictures.css".'" class="ui-corner-all pictures-collection slide" id="picture-'.$picture->Id.'">';
                echo '<div>'.ucfirst($picture->PictureDate).'</div>';
                echo CHtml::hiddenField('internalId', $picture->Id, array('id'=>'picture-id'));
                    echo '<div>';
                    echo $this->actionPicture($picture->Id);
//                                echo CHtml::image(Yii::app()->getBaseUrl() . "/assets/2016_11_16_15_55_35_21327569336-thumbnail.jpg");
//                                echo CHtml::image(Yii::app()->getBaseUrl() . "/cropPictures/picture?picture_id=".$picture->Id);
//                                echo Yii::app()->getBaseUrl() . "/cropPictures/picture?picture_id=".$picture->Id;                               
                    echo '</div>';
                echo '</div>';
            echo '</div>';
    endforeach;
    }
    //    function to return from the comparison of a plant to the collection previously selected
    public function actionReturnCollection($collection_date) {
        $selected_picture = '';
        $pictures = CropPictures::model()->getBySequenceDate($collection_date);
        $selected_picture = $this->get_first($selected_picture, $pictures)->Id;
//        $this->renderPartial('picture_slider', array('pictures' => $pictures, 'selected_picture'=>$selected_picture)); 
        foreach($pictures as $picture):
            echo '<div rel="stylesheet" href="'.Yii::app()->request->baseUrl."/css/crop-pictures.css".'" class="ui-corner-all pictures-collection slide" id="picture-'.$picture->Id.'">';
                echo '<div>'.ucfirst($picture->PictureDate).'</div>';
                echo CHtml::hiddenField('internalId', $picture->Id, array('id'=>'picture-id'));
                    echo '<div>';
                    echo $this->actionPicture($picture->Id);
//                                echo CHtml::image(Yii::app()->getBaseUrl() . "/assets/2016_11_16_15_55_35_21327569336-thumbnail.jpg");
//                                echo CHtml::image(Yii::app()->getBaseUrl() . "/cropPictures/picture?picture_id=".$picture->Id);
//                                echo Yii::app()->getBaseUrl() . "/cropPictures/picture?picture_id=".$picture->Id;                               
                    echo '</div>';
                echo '</div>';
            echo '</div>';
    endforeach;
    }
    
//    this function gets the same plant in different time
    public function actionComparePictures($picture_id) {
        $picture = CropPictures::model()->findByPk($picture_id);
        if($picture != NULL){
            $pictures = CropPictures::model()->getCloserPlants($picture->Latitude,$picture->Longitude,2);
    //        $this->renderPartial('picture_slider', array('pictures' => $pictures, 'selected_picture'=>$selected_picture)); 
            foreach($pictures as $picture):
                echo '<div rel="stylesheet" href="'.Yii::app()->request->baseUrl."/css/crop-pictures.css".'" class="ui-corner-all pictures-collection slide" id="picture-'.$picture->Id.'">';
                    echo '<div>'.ucfirst($picture->PictureDate).'</div>';
                    echo CHtml::hiddenField('internalId', $picture->Id, array('id'=>'picture-id'));
                        echo '<div>';
                        echo $this->actionPicture($picture->Id);                           
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            endforeach;
        }
    }
    
//    this function gets infected plants, current implementation choose a random plant.
//    
    public function actionInfected() {
        $picture = CropPictures::model()->findByPk('44');
        if($picture != NULL){
            echo '<div rel="stylesheet" href="'.Yii::app()->request->baseUrl."/css/crop-pictures.css".'" class="ui-corner-all pictures-collection slide" id="picture-'.$picture->Id.'">';
                echo '<div>'.ucfirst($picture->PictureDate).'</div>';
                echo CHtml::hiddenField('internalId', $picture->Id, array('id'=>'picture-id'));
                    echo '<div>';
                    echo $this->actionPicture($picture->Id);                           
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        }
    }

//    ginven a picture id, returns the img tag contains it
    public function actionPicture($picture_id){
        $db_picture = CropPictures::model()->findByPk($picture_id);
        $split_name = explode('.',$db_picture->Picturepath);
        $ftp_path = $split_name[0].'-thumbnail.'.$split_name[1];
        $picture_name = explode('/',$ftp_path);
        $picture_name = $picture_name[count($picture_name)-1];
        
        return "<img src='assets/".$picture_name."'/>";
    }
//    same as actionPicture, but for the preview
    
    public function actionPicturePreview($picture_id){
        $db_picture = CropPictures::model()->findByPk($picture_id);
        $split_name = explode('.',$db_picture->Picturepath);
        $ftp_path = $split_name[0].'-thumbnail.'.$split_name[1];
        $picture_name = explode('/',$ftp_path);
        $picture_name = $picture_name[count($picture_name)-1];
        
        return "<label for='preview_picture'>".Yii::t('UserModule.dashboard', 'Picture Preview')."</label>"
                . "<img id='preview-picture' src='assets/".$picture_name."'/>";
    } 
    
    //    function that gets all information about a picture trough picture id 
    //    returns a JSON containing them.
    public function actionPictureInformation($picture_id) {
        $picture = CropPictures::model()->findByPk($picture_id);
        $img_picture = '';
        $unknown = 'Unknown';
        $picture_date = $unknown;
        $latitude = $unknown;
        $longitude = $unknown;
        $user = $unknown;
        $device = $unknown;
        $crop = $unknown;
        $humidity = $unknown;
        $temperature = $unknown;
        $lw = $unknown;
        $rain = $unknown;
                
        if($picture != NULL){
            $img_picture = $this->actionPicturePreview($picture_id);
            $picture_date = $picture->PictureDate;
            $latitude = $picture->Latitude;
            $longitude = $picture->Longitude;
            $user = $picture->User;
            $device = $picture->Device;
            $crop = $picture->CropName;
            if($picture->Humidity != NULL){
                $humidity = $picture->Humidity.' %';
            }
            if($picture->Temperature != NULL){
                $temperature = $picture->Temperature.' °C';
            }
            if($picture->LW != NULL){
                $lw = $picture->LW;
            }
            if($picture->Rain != NULL){
                $rain = $picture->Rain;
            }
        }
        echo CJSON::encode(array('img_picture' => $img_picture, 'date' => $picture_date,
            'latitude' => $latitude, 'longitude' => $longitude, 'user'=>$user, 'device'=>$device,
            'temperature'=>$temperature,'humidity'=>$humidity,'rain'=>$rain, 'lw'=>$lw, 'crop'=>$crop));    

        }
    
//    function used to create a jpg file in assets and return its path
    public function get_picture($picture_name){
        $assets = 'assets/';
        $picture_path = $assets.$picture_name;
        if(substr($picture_name, -4) == '.jpg' and strlen($picture_name) > 4){
            if(file_exists($picture_path)){
                return $picture_path;
            }
            else{
                $fp = fopen($picture_path, 'w');
                if($fp){
                    fclose($fp);
                    return $picture_path;
                }
            }
        }
        return false;
    }
    
//    util to find the first elements of a list if exists (model for example)
    public function get_first($first, $list){
        if($first == ''){
            if(count($list) >= 1){
                return $list[0];
            }
        }
        return '';
    }
} 