

Espo.define('views/preferences/edit', 'views/edit', function (Dep) {

    return Dep.extend({

        userName: '',

        setup: function () {
            Dep.prototype.setup.call(this);
            this.userName = this.model.get('name');
            // snapp
            Espo.Utils.setRtlStatus(this.model.get('language'));
            // Espo.Utils.isRtl() ? Espo.Utils.addMetaLink() : '';
            // end snapp
        },

        getHeader: function () {
            var html = '';
            html += this.translate('Preferences');
            html += ' &raquo ';
            html += this.userName;
            return html;
        },

    });
});
