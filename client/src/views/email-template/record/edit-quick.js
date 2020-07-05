

Espo.define('views/email-template/record/edit-quick', ['views/record/edit', 'views/email-template/record/detail'], function (Dep, Detail) {

    return Dep.extend({

    	isWide: true,

        sideView: false,

        setup: function () {
            Dep.prototype.setup.call(this);
            Detail.prototype.listenToInsertField.call(this);
        },

    });
});
