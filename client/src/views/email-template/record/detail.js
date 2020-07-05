

Espo.define('views/email-template/record/detail', 'views/record/detail', function (Dep) {

    return Dep.extend({

        duplicateAction: true,

        setup: function () {
            Dep.prototype.setup.call(this);
            this.listenToInsertField();
        },

        listenToInsertField: function () {
            this.listenTo(this.model, 'insert-field', function (o) {
                var tag = '{' + o.entityType + '.' + o.field + '}';

                var bodyView = this.getFieldView('body');
                if (!bodyView) return;

                if (this.model.get('isHtml')) {
                    bodyView.$summernote.summernote('insertText', tag);
                } else {
                    var $body = bodyView.$element;
                    var text = $body.val();
                    text += tag;
                    $body.val(text);
                }
            }, this);
        },
    });
});
