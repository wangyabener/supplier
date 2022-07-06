<?php

use yii\db\Migration;
use Hashids\Hashids;

/**
 * Handles the creation of table `{{%suppliers}}`.
 */
class m220629_030024_create_suppliers_table extends Migration
{
    private $table = '{{%suppliers}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->comment('Supplier name'),
            'code' => $this->string(3)->notNull()->comment('Supplier Unique identification code'),
            'mobile' => $this->string(11)->notNull()->comment('Supplier mobile'),
            't_status' => "enum('ok', 'hold') CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT 'ok'",
            'description' => $this->text(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB');

        // add index
        $this->createIndex('uk_code', $this->table, 'code', true);
        $this->createIndex('idx_mobile', $this->table, 'mobile');

        // table comment
        $this->addCommentOnTable($this->table, 'Supplier master table');

        // Randomly generated username
        $firstname = ['Smith', 'Jones', 'Taylor', 'LiWilson', 'Roberts', 'Evans', 'Brown', 'Jackson', 'Thompson', 'Lewis'];
        $lastname = [
            'James', 'John', 'Robert', 'Michael', 'William', 'David', 'Richard', 'Thomas',
            'Mary', 'Linda', 'Maria', 'Nancy', 'Lisa', 'Betty', 'Barbara', 'Helen', 'Jennifer'
        ];
        $usernames = [];
        for ($i = 0; $i < 25; $i++) {
            $usernames[] = $firstname[array_rand($firstname)] . ' ' 
                . $lastname[array_rand($lastname)];
        }
        $mobiles = ['131', '188', '176', '159', '186', '155'];

        // Generate code
        $hashids = new Hashids('Supplier', 3);

        // Init data
        $suppliers = [];
        foreach ($usernames as $key => $item) {
            $suppliers[] = [
                $key + 1,
                $item,
                $hashids->encode($key + 1),
                $mobiles[array_rand($mobiles)] . mt_rand(1000, 9999) . mt_rand(1000, 9999),
                (0 === $key % 2) ? 'ok' : 'hold',
                '',
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s')
            ];
        }

        // Insert
        $columns = ['id', 'name', 'code', 'mobile', 't_status', 'description', 'created_at', 'updated_at'];
        $this->batchInsert($this->table, $columns, $suppliers);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->table);
    }
}
