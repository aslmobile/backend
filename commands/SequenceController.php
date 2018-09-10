<?php

namespace app\commands;

use app\components\ConsoleController;

class SequenceController extends ConsoleController
{
    public function actionUp()
    {
        $this->up();
    }

    public function actionDown()
    {
        $this->down();
    }

    public function actionRefresh()
    {
        $this->down();
        $this->up();
    }

    public function up()
    {
        $connection = \Yii::$app->getDb();
        $dbSchema = $connection->getSchema();
        $tables = $dbSchema->getTableNames();
        foreach ($tables as $tbl) {
            $table = $connection->getTableSchema($tbl);
            if (isset($table->columns['id'])) {
                $start = intval($connection->createCommand('SELECT MAX(id) from "' . $tbl . '"')->queryOne()['max']);
                $start++;
                $connection->createCommand('CREATE SEQUENCE ' . $tbl . '_id_seq START ' . $start . ' 
                    MINVALUE 1 MAXVALUE 99999999999 OWNED BY "' . $tbl . '"."id";')
                    ->execute();
                $connection->createCommand('ALTER TABLE "' . $tbl . '" ALTER id SET DEFAULT nextval(\'' . $tbl . '_id_seq\')')
                    ->execute();
            }
        }
    }

    public function down()
    {
        $connection = \Yii::$app->getDb();
        $dbSchema = $connection->getSchema();
        $tables = $dbSchema->getTableNames();
        foreach ($tables as $tbl) {
            $table = $connection->getTableSchema($tbl);
            if (isset($table->columns['id'])) {
                $connection->createCommand('DROP SEQUENCE IF EXISTS ' . $tbl . '_id_seq CASCADE')
                    ->execute();
            }
        }
    }
}
