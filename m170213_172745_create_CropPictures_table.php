<?php

class m170213_172745_create_CropPictures_table extends CDbMigration
{
    private $table = 'CropPictures';
	public function up()
	{
            if (!($this->getDbConnection()->schema->getTable($this->table) === null)) {
                echo "m140215_161121_create_crop_run_table: Table `" . $this->table . "` exists. Removing  it... ";
                $this->dropTable($this->table);
            }
            $this->table = 'CropPictures';
            $this->createTable($this->table, array(
                'Id' => 'INT NOT NULL AUTO_INCREMENT',
                'CropName' => 'varchar(64) NOT NULL',
                'PictureDate' => 'datetime NOT NULL',
                'SequenceDate' => 'datetime NOT NULL',
                'SequenceName' => 'varchar(255) NOT NULL',
                'Picturepath' => 'varchar(255)',
                'Latitude' => 'float',
                'Longitude' => 'float',
                'Device' => 'varchar(255)',
                'User' => 'varchar(64)',
                'ReceivedTS' => 'datetime',
                'Temperature' => 'float',
                'TempDate' => 'datetime',
                'Humidity' => 'float',
                'HumDate' => 'datetime',
                'LW' => 'float',
                'LWDate' => 'datetime',
                'Rain' => 'float',
                'RainDate' => 'datetime',
            ));

            $this->addPrimaryKey('PRIMARYKEY', $this->table, 'Id');
            }

	public function down(){
            if (!($this->getDbConnection()->schema->getTable($this->table) === null)) {
                echo "m140215_161121_create_crop_run_table: Table `" . $this->table . "` exists. Removing  it... ";
                $this->dropTable($this->table);
            } else {
                echo "Table `" . $this->table . "` does not exists. Nothing done.";
        }
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}