 <?php

/**
 * This is the model class for table "CropPictures".
 *
 * The followings are the available columns in table 'CropPictures':
 * @property integer $Id
 * @property string $CropName
 * @property string $PictureDate
 * @property string $SequenceDate
 * @property string $SequenceName
 * @property string $Picturepath
 * @property double $Latitude
 * @property double $Longitude
 * @property string $Device
 * @property string $User
 * @property string $ReceivedTS
 * @property double $Temperature
 * @property string $TempDate
 * @property double $Humidity
 * @property string $HumDate
 * @property double $LW
 * @property string $LWDate
 * @property double $Rain
 * @property string $RainDate
 */
class CropPictures extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CropPictures the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'CropPictures';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('CropName, PictureDate, SequenceDate, SequenceName', 'required'),
            array('Latitude, Longitude, Temperature, Humidity, LW, Rain', 'numerical'),
            array('CropName, User', 'length', 'max'=>64),
            array('SequenceName, Picturepath, Device', 'length', 'max'=>255),
            array('ReceivedTS, TempDate, HumDate, LWDate, RainDate', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('Id, CropName, PictureDate, SequenceDate, SequenceName, Picturepath, Latitude, Longitude, Device, User, ReceivedTS, Temperature, TempDate, Humidity, HumDate, LW, LWDate, Rain, RainDate', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'Id' => 'ID',
            'CropName' => 'Crop Name',
            'PictureDate' => 'Picture Date',
            'SequenceDate' => 'Sequence Date',
            'SequenceName' => 'Sequence Name',
            'Picturepath' => 'Picturepath',
            'Latitude' => 'Latitude',
            'Longitude' => 'Longitude',
            'Device' => 'Device',
            'User' => 'User',
            'ReceivedTS' => 'Received Ts',
            'Temperature' => 'Temperature',
            'TempDate' => 'Temp Date',
            'Humidity' => 'Humidity',
            'HumDate' => 'Hum Date',
            'LW' => 'Lw',
            'LWDate' => 'Lwdate',
            'Rain' => 'Rain',
            'RainDate' => 'Rain Date',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('Id',$this->Id);
        $criteria->compare('CropName',$this->CropName,true);
        $criteria->compare('PictureDate',$this->PictureDate,true);
        $criteria->compare('SequenceDate',$this->SequenceDate,true);
        $criteria->compare('SequenceName',$this->SequenceName,true);
        $criteria->compare('Picturepath',$this->Picturepath,true);
        $criteria->compare('Latitude',$this->Latitude);
        $criteria->compare('Longitude',$this->Longitude);
        $criteria->compare('Device',$this->Device,true);
        $criteria->compare('User',$this->User,true);
        $criteria->compare('ReceivedTS',$this->ReceivedTS,true);
        $criteria->compare('Temperature',$this->Temperature);
        $criteria->compare('TempDate',$this->TempDate,true);
        $criteria->compare('Humidity',$this->Humidity);
        $criteria->compare('HumDate',$this->HumDate,true);
        $criteria->compare('LW',$this->LW);
        $criteria->compare('LWDate',$this->LWDate,true);
        $criteria->compare('Rain',$this->Rain);
        $criteria->compare('RainDate',$this->RainDate,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    public function getPicturesBetweenDate($crop_name, $from, $to) {
        $query = "SELECT * FROM CropPictures WHERE CropName=" . $crop_name . " AND PictureDate BETWEEN '" . $from
                . "' AND '" . $to . "';";
        $pictures = $this->findAllBySql($query);
        return $pictures;
    }
    
    public function getUserCrops($user_id){
        if(!User::model()->isAdmin()){
            $query = "SELECT DISTINCT PictureCrop.CropName FROM"
                    . " (SELECT DISTINCT Crop.name AS CropName FROM (SELECT crop_id FROM crop_has_users WHERE user_id=". $user_id .") as Id_crops, crop as Crop WHERE Crop.id_crop=Id_crops.crop_id) AS UserCrops,"
                    . " CropPictures AS PictureCrop WHERE"
                    . " UserCrops.CropName=PictureCrop.CropName;";
        }
        else{
            $query = "SELECT DISTINCT CropName FROM CropPictures;";
        }
        $crops = $this->findAllBySql($query);
        return $crops;
    }
    
    public function getLastCropCollection($crop_name){
        $query = "SELECT pics.* FROM CropPictures AS pics,
             (SELECT max(SequenceDate) AS max_data FROM CropPictures WHERE CropName='".$crop_name."') AS T1
             WHERE pics.SequenceDate=T1.max_data AND CropName='". $crop_name ."';";
        $last_pictures = $this->findAllBySql($query);
        return $last_pictures;
    }
    public function getCropCollections($cropName,$dateFrom,$dateTo){
       $query = "SELECT DISTINCT SequenceDate FROM CropPictures WHERE "
               . "CropName ='".$cropName."' AND SequenceDate BETWEEN '".$dateFrom."' AND '".$dateTo."';";
       $collections = $this->findAllBySql($query);
        return $collections;
    }
    
    public function getCloserPlants($latitude, $longitude, $range) {
        $range = $range/100000;
        $query = "SELECT *
             FROM CropPictures
             WHERE (abs(Latitude-".$latitude.")<= ".$range.")
             AND (abs(Longitude-".$longitude.")<= ".$range.");";
        $closer_plants = $this->findAllBySql($query);
        return $closer_plants;
    }
    public function getBySequenceDate($sequence_date){
        $query = "SELECT *
             FROM CropPictures WHERE SequenceDate='".$sequence_date."';";
        $closer_plants = $this->findAllBySql($query);
        return $closer_plants;
    }
} 