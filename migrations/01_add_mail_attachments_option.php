<?php

class AddMailAttachmentsOption extends Migration {

    public function up() {
        Config::get()->create("BACHELOR_HOMEWORK_FOLDER_MAIL_ATTACHMENT", array(
            'value' => 0,
            'type' => "boolean",
            'range' => "global",
            'section' => "BACHELOR_HOMEWORK_FOLDER",
            'description' => "Should the e-mails from this plugin have the file as an attachment?"
        ));
    }

    public function down()
    {
        Config::get()->delete("BACHELOR_HOMEWORK_FOLDER_MAIL_ATTACHMENT");
    }

}
