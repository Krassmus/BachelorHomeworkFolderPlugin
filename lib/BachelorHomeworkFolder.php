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
        $text .= "\n\nDateiname: ".$fileref->name."\nEingereicht: ".date("d.m.Y H:i:s");
        $subject = $this->folderdata['data_content']['subject'];
        $emails = preg_split(
            "/\s+/",
            $this->folderdata['data_content']['emails'],
            -1,
            PREG_SPLIT_NO_EMPTY
        );
        foreach ($emails as $email) {
            StudipMail::sendMessage(
                $email,
                $subject,
                $text
            );
        }

        $messaging = new messaging();
        $messaging->insert_message(
            $text,
            get_username(),
            "____%system%____",
            '',
            '',
            '',
            '',
            $subject,
            true,
            'normal',
            ['Abgabe']
        );

        return $fileref;
    }
}