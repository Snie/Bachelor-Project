<?php

/* @var $this CropPicturesController */

$this->breadcrumbs=array(
    'Crop Pictures',
);

Yii::app()->clientScript->registerCssFile("http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/ui-darkness/jquery-ui.css");
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/css/crop-pictures.css");
?>
<div class="crop-pictures-filters">
<label for="<?php echo 'pictures-results-date-from';?>"><?php echo Yii::t('UserModule.dashboard', 'From');?>:</label>
    <?php echo CHtml::textField('dateFrom', '', array('class'=>'pictures-results-filter-date', 'id'=>'pictures-results-date-from')); ?>
<label for="<?php echo 'pictures-results-date-to';?>"><?php echo Yii::t('UserModule.dashboard', 'To');?>:</label>
    <?php echo CHtml::textField('dateTo', '', array('class'=>'pictures-results-filter-date', 'id'=>'pictures-results-date-to')); ?>
<?php echo CHtml::hiddenField('internalDateFrom', '', array('class'=>'pictures-results-filter-date', 'id'=>'pictures-results-internal-date-from')); ?>
<?php echo CHtml::hiddenField('internalDateTo', '', array('class'=>'pictures-results-filter-date', 'id'=>'pictures-results-internal-date-to')); ?>
<label for="<?php echo 'crop-dropdown';?>"><?php echo Yii::t('UserModule.dashboard', 'Crop');?>:</label>
<?php
echo CHtml::dropDownList('crop-dropdown', $selected_crop, CHtml::listData($crops, 'CropName', 'CropName') , array('empty' => 'select a Crop',
                                                'ajax' => array('type' => 'GET',
                        'url' => Yii::app()->createUrl('cropPictures/loadCollections'),
                        'update' => '#collection-dropdown', //selector to update
                        'data' => array('cropName' => 'js:this.value', 'dateFrom'=>$date_from,'dateTo'=>$date_to), //data
                                                ))); 
?>
<label for="<?php echo 'collection-dropdown';?>"><?php echo Yii::t('UserModule.dashboard', 'Collection');?>:</label>
<?php
echo CHtml::dropDownList('collection-dropdown',$selected_collection ,CHtml::listData($collections, 'SequenceDate', 'SequenceDate'), array('empty'=>'Select a Sequence',
                                                'ajax' => array('type' => 'GET',
                        'url' => Yii::app()->createUrl('cropPictures/updatePictures'),
                        'data' => array('collection_date' => 'js:this.value'),
                                                'beforeSend' => 'function() {'
                                                    . '}',
                                                'success' => 'function(data) {'
                                                    . '$("#pictures-list-content").html(data);'
                                                    . 'setupCarouselClick();'
                                                    . '$("#pictures-list-content").trigger("updateSizes");'
                                                    . '$("#compare-button").val("Compare plant over time");'
                                                    . 'last_collection= undefined;'
                                                    . '}',
                                                ))) 
?>
<?php echo CHtml::button(Yii::t('UserModule.dashboard', 'Filter collections'), array('class'=>'pictures-results-filter-button btn', 'id'=>'pictures-results-filter-button')); ?>
<?php echo CHtml::button(Yii::t('UserModule.dashboard', 'Show infected plants'), array('class'=>'pictures-results-infection-button btn', 'id'=>'pictures-results-infection-button')); ?>
<?php echo '<script type="text/javascript">',
        "setupDatePicker('".$date_from."','".$date_to."');",
        '</script>';
?>
</div>
<div class="pictures-details">
    <div class="picture-preview" id="picture-preview-image">
        <?php if($selected_picture != ''){ ?>
            <?php echo $this->actionPicturePreview($selected_picture);
            echo '<script type="text/javascript">',
                "setupPictureInformation(".$selected_picture.");",
                '</script>';
            ?>
            
        <?php }else{?>
            <?php echo 'No picture selected'?>
        <?php } ?>
    </div>
    <?php echo CHtml::button(Yii::t('UserModule.dashboard', 'Compare plant over time'), array('class'=>'compare-plant-button btn', 'id'=>'compare-button')); ?>
    <div class="picture-data" >
        <div class='picture-geodata'>
            <label for="<?php echo 'picture-date';?>" class="info_label"><?php echo Yii::t('UserModule.dashboard', 'Picture date');?>:</label>
        <?php echo '<p class="picture_information" id="picture-date"></p>'?>
        <label for="<?php echo 'picture-crop';?>" class="info_label"><?php echo Yii::t('UserModule.dashboard', 'Crop');?>:</label>
        <?php echo '<p class="picture_information" id="picture-crop"></p>'?>
        <label for="<?php echo 'picture-latitude';?>" class="info_label"><?php echo Yii::t('UserModule.dashboard', 'Picture latitude');?>:</label>
        <?php echo '<p class="picture_information" id="picture-latitude"></p>'?>
        <label for="<?php echo 'picture-longitude';?>" class="info_label"><?php echo Yii::t('UserModule.dashboard', 'Picture longitude');?>:</label>
        <?php echo '<p class="picture_information" id="picture-longitude"></p>'?>
        <label for="<?php echo 'picture-user';?>" class="info_label"><?php echo Yii::t('UserModule.dashboard', 'Picture user');?>:</label>
        <?php echo '<p class="picture_information" id="picture-user"></p>'?>
        <label for="<?php echo 'picture-device';?>" class="info_label"><?php echo Yii::t('UserModule.dashboard', 'Device used');?>:</label>
        <?php echo '<p class="picture_information" id="picture-device"></p>'?>
        </div>
        <div class="picture-microclimate">
        <label for="<?php echo 'picture-temp';?>" class="info_label"><?php echo Yii::t('UserModule.dashboard', 'Temperature');?>:</label>
        <?php echo '<p class="picture_information" id="picture-temp"></p>'?>
        <label for="<?php echo 'picture-hum';?>" class="info_label"><?php echo Yii::t('UserModule.dashboard', 'Humidity');?>:</label>
        <?php echo '<p class="picture_information" id="picture-hum"></p>'?>
        <label for="<?php echo 'picture-lw';?>" class="info_label"><?php echo Yii::t('UserModule.dashboard', 'Leaf wetness');?>:</label>
        <?php echo '<p class="picture_information" id="picture-lw"></p>'?>
        <label for="<?php echo 'picture-rain';?>" class="info_label"><?php echo Yii::t('UserModule.dashboard', 'Rain');?>:</label>
        <?php echo '<p class="picture_information" id="picture-rain"></p>'?>
        </div>
        
    </div>
    <div id="picture-map"></div>
</div>
<div class='error' style='display:none'>No pictures found</div>
<?php $this->renderPartial('picture_slider', array('pictures' => $pictures, 'selected_picture'=>$selected_picture)); ?>


