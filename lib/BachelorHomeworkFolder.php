<?php

class BachelorHomeworkFolder extends HomeworkFolder
{
    public function __construct($folderdata = null)
    {
        parent::__construct($folderdata);
        $this->permission = 3;
    }

    public static function getTypeName()
    {
        return _('Ordner für Abschlussarbeiten');
    }

    public function isWritable($user_id = null)
    {
        return true;
    }

    public static function availableInRange($range_id_or_object, $user_id)
    {
        $range_id = is_object($range_id_or_object) ? $range_id_or_object->id : $range_id_or_object;
        return Seminar_Perm::get()->have_studip_perm('tutor', $range_id, $user_id);
    }

    public function getIcon($role = Icon::DEFAULT_ROLE)
    {
        $shape = count($this->getSubfolders()) + count($this->getFiles()) === 0
            ? 'folder-doctoralcap-empty.svg'
            : 'folder-doctoralcap-full.svg';
        $shape = $GLOBALS['ABSOLUTE_URI_STUDIP'].'plugins_packages/data-quest/BachelorHomeworkFolderPlugin/assets/' . $shape;
        return Icon::create($shape, $role);
    }

    public function getEditTemplate()
    {
        $tf = new Flexi_TemplateFactory(__DIR__."/../views");
        $template = $tf->open("folder/edit.php");
        $template->folder = $this;
        $template->folderdata = $this->folderdata;
        return $template;
    }

    public function setDataFromEditTemplate($request)
    {
        $this->folderdata['data_content']['emails']   = $request['bhf_emails'];
        $this->folderdata['data_content']['subject']  = $request['bhf_subject'];
        $this->folderdata['data_content']['body']     = $request['bhf_body'];
        return parent::setDataFromEditTemplate($request);
    }

    public function getDescriptionTemplate()
    {
    }

    public function createFile($file)
    {
        $fileref = parent::createFile($file);
        //send an email
        $text = $this->folderdata['data_content']['body'];


        $text = preg_replace(
            "/".preg_quote("{{username}}")."/",
            $GLOBALS['user']->username,
            $text
        );
        $text = preg_replace(
            "/".preg_quote("{{email}}")."/i",
            $GLOBALS['user']->email,
            $text
        );
        $text = preg_replace(
            "/".preg_quote("{{first_name}}")."/i",
            $GLOBALS['user']->vorname,
            $text
        );
        $text = preg_replace(
            "/".preg_quote("{{last_name}}")."/i",
            $GLOBALS['user']->nachname,
            $text
        );
        foreach (DataField::findBySQL("object_type = 'user'") as $datafield) {
            $value = DatafieldEntryModel::findOneBySQL("datafield_id = :datafield_id AND range_id = :user_id", [
                'datafield_id' => $datafield->getId(),
                'user_id' => $GLOBALS['user']->id
            ]);
            $text = preg_replace(
                "/".preg_quote("{{".$datafield['name']."}}")."/i",
                $value['content'],
                $text
            );
        }

        $text .= "\n\nDateiname: ".$fileref->name."\nEingereicht: ".date("d.m.Y H:i:s");
        $subject = $this->folderdata['data_content']['subject'];
        $emails = preg_split(
            "/\s+/",
            $this->folderdata['data_content']['emails'],
            -1,
            PREG_SPLIT_NO_EMPTY
        );
        foreach ($emails as $email) {
            $mail = new StudipMail();
            $mail->setSubject($subject);
            $mail->addRecipient($email);
            $mail->setBodyText($text);
            $mail->addStudipAttachment($fileref);
            $mail->send();
        }

        do {
            $message_id = md5(uniqid());
        } while (Message::find($message_id));

        $attachment_folder = MessageFolder::createTopFolder($message_id);
        FileManager::copyFileRef(
            $fileref,
            $attachment_folder,
            User::findCurrent()
        );

        $messaging = new messaging();
        $messaging->insert_message(
            $text,
            get_username(),
            "____%system%____",
            '',
            $message_id,
            '',
            '',
            $subject,
            true,
            'normal',
            ['Abgabe']
        );

        return $fileref;
    }

    public function getFiles()
    {
        // We must load the files (FileRefs) directly from the database
        // since files that were added to this folder object after it was
        // created are not included in the file_refs attribute:
        if ($GLOBALS['perm']->have_studip_perm('dozent', $this->range_id, $GLOBALS['user']->id)) {
            return FileRef::findByFolder_id($this->getId(), "ORDER BY name");
        } else {
            return FileRef::findBySQL("folder_id = ? AND user_id = ? ORDER BY name", [$this->getId(), $GLOBALS['user']->id]);
        }

    }

    public function isReadable($user_id = null)
    {
        return true;
    }

    public function isFileDownloadable($fileref_or_id, $user_id)
    {
        return $GLOBALS['perm']->have_perm("root") || ($GLOBALS['perm']->have_studip_perm('dozent', $this->range_id, $GLOBALS['user']->id)
                && !$GLOBALS['perm']->have_studip_perm('admin', $this->range_id, $GLOBALS['user']->id));
    }
}
