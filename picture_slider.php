<div class="html_carousel">
    <div class="pictures-list" id="pictures-list-content">
        <?php if(count($pictures) > 0) {?>    
        <?php foreach($pictures as $picture) :?>
            <div class="ui-corner-all pictures-collection <?php echo ($selected_picture == $picture->Id) ? "picture-selected ui-selected" : ""; ?> slide" id="picture-<?php echo $picture->Id;?>">
                <div><?php echo ucfirst($picture->PictureDate); ?></div>
                <?php echo CHtml::hiddenField('internalId', $picture->Id, array('id'=>'picture-id')); ?>
                <div>
                <?php 
                echo $this->actionPicture($picture->Id);?>
                </div>
            </div>
    <?php endforeach;  ?>
        </div>
        <?php }else{?>
            <div class="no-pictures-carousell" id="no-pictures">
                <?php echo 'No pictures to show'; ?>
            </div>
        <?php }?>
    <div class="clearfix"></div>
    <?php echo CHtml::button(Yii::t('UserModule.dashboard', 'Previous'), array('class'=>'carousel_button', 'id'=>'foo2_prev')); ?>
    <?php echo CHtml::button(Yii::t('UserModule.dashboard', 'Next'), array('class'=>'carousel_button', 'id'=>'foo2_next')); ?>
    <div class="pagination" id="foo2_pag" ></div>
    <?php echo '<script type="text/javascript">',
    "setupCarousel();",
    '</script>';
    ?>
</div>  