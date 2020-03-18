<?= _("Dieser Ordner schickt eine schriftliche Empfangsbestätigung an die angegebenen Emailadressen und den Hochladenden, wenn eine Arbeit eingereicht worden ist.") ?>

<label>
    <?= _("Emailadressen für Empfangsbestätigung") ?>
    <input name="bhf_emails" type="text" placeholder="test@example.com test2@example.com ..." value="<?= htmlReady($folderdata['data_content']['emails']) ?>">
</label>

<label>
    <?= _("Betreff der Mail") ?>
    <input type="text" name="bhf_subject" value="<?= htmlReady($folderdata['data_content']['subject']) ?>" placeholder="<?= _("Abschlussarbeit") ?>">
</label>

<label>
    <?= _("Template für die Mail") ?>
    <textarea name="bhf_body" placeholder="<?= _("Mit dieser Nachricht ist der Eingang Ihrer Arbeit bestätigt.") ?>"><?= htmlReady($folderdata['data_content']['body']) ?></textarea>
</label>
