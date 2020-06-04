<?php

class ForwardController extends PluginController
{
    public function message_action($fileref_id)
    {
        $fileref = new FileRef($fileref_id);
        if (!$fileref->foldertype->isFileDownloadable($fileref_id, $GLOBALS['user']->id)) {
            throw new AccessDeniedException();
        }

        do {
            $message_id = md5(uniqid());
        } while(Message::find($message_id));

        $attachment_folder = MessageFolder::createTopFolder($message_id);
        FileManager::copyFileRef(
            $fileref,
            $attachment_folder,
            User::findCurrent()
        );

        $this->redirect(URLHelper::getURL("dispatch.php/messages/write", [
            'message_id' => $message_id,
            'message_subject' => "Abgabe"
        ]));
    }
}
