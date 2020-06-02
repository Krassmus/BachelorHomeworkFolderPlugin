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
    <textarea name="bhf_body"
              style="height: 200px;"
              placeholder="<?= _("Mit dieser Nachricht ist der Eingang Ihrer Arbeit bestätigt.") ?>"><?= htmlReady($folderdata['data_content']['body']) ?></textarea>
</label>

<div>
    <?= Icon::create("info-circle", "info")->asImg(16, ['title' => _("Wenn in dem obigen Text einer dieser Platzhalter auftaucht, wird dieser ersetzt durch den entsprechenden Wert des Hochladenden."), 'class' => "text-bottom"]) ?>
    <?= _("Mögliche Platzhalter") ?>:
    <a href="" onClick="$('[name=bhf_body]').val($('[name=bhf_body]').val() + ' ' + '{{username}}'); return false;">{{username}}</a>,
    <a href="" onClick="$('[name=bhf_body]').val($('[name=bhf_body]').val() + ' ' + '{{email}}'); return false;">{{email}}</a>,
    <a href="" onClick="$('[name=bhf_body]').val($('[name=bhf_body]').val() + ' ' + '{{first_name}}'); return false;">{{first_name}}</a>,
    <a href="" onClick="$('[name=bhf_body]').val($('[name=bhf_body]').val() + ' ' + '{{last_name}}'); return false;">{{last_name}}</a><?
    foreach (DataField::findBySQL("object_type = 'user'") as $datafield) : ?>,
        <a href="" onClick="$('[name=bhf_body]').val($('[name=bhf_body]').val() + ' ' + '{{<?= htmlReady($datafield['name']) ?>}}'); return false;">{{<?= htmlReady($datafield['name']) ?>}}</a><?
    endforeach ?>

</div>
