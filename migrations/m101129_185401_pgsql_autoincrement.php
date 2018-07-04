<?php
/**
 * Created by PhpStorm.
 * User: vitarr
 * Date: 21.03.18
 * Time: 15:24
 */

use yii\db\Migration;

class m101129_185401_pgsql_autoincrement extends Migration
{

    /**
     * @return bool|void
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $connection = Yii::$app->getDb();
        $dbSchema = $connection->getSchema();
        $tables = $dbSchema->getTableNames();
        foreach ($tables as $tbl) {
            $table = $connection->getTableSchema($tbl);
            if (isset($table->columns['id'])) {
                $start = intval($connection->createCommand('SELECT MAX(id) from "' . $tbl . '"')->queryOne()['max']);
                $start++;
                $this->execute(
                    'CREATE SEQUENCE IF NOT EXISTS ' . $tbl . '_id_seq START ' . $start . ' 
                    MINVALUE 1 MAXVALUE 99999999999 OWNED BY "' . $tbl . '"."id";'
                );
                $this->execute('ALTER TABLE "' . $tbl . '" ALTER id SET DEFAULT nextval(\'' . $tbl . '_id_seq\')');
            }
        }
        parent::safeUp();
    }

    public function safeDown()
    {
        $connection = Yii::$app->getDb();
        $dbSchema = $connection->getSchema();
        $tables = $dbSchema->getTableNames();
        foreach ($tables as $tbl) {
            $table = $connection->getTableSchema($tbl);
            if (isset($table->columns['id'])) {
                $this->execute('DROP SEQUENCE IF EXISTS ' . $tbl . '_id_seq CASCADE');
            }
        }
        parent::safeDown();
    }
}
