

Espo.define('crm:views/opportunity/record/panels/activities', 'crm:views/record/panels/activities', function (Dep) {

    return Dep.extend({

        getComposeEmailAttributes: function (scope, data, callback) {
            data = data || {};
            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

            Dep.prototype.getComposeEmailAttributes.call(this, scope, data, function (attributes) {
                this.ajaxGetRequest('Opportunity/action/emailAddressList?id=' + this.model.id).then(function (list) {
                    attributes.to = '';
                    attributes.cc = '';
                    attributes.nameHash = {};

                    list.forEach(function (item, i) {
                        attributes.to += item.emailAddress + ';';
                        attributes.nameHash[item.emailAddress] = item.name;
                    });
                    Espo.Ui.notify(false);

                    callback.call(this, attributes);

                }.bind(this));
            }.bind(this))
        }
    });
});
