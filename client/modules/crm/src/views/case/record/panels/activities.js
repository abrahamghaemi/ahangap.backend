

Espo.define('crm:views/case/record/panels/activities', 'crm:views/record/panels/activities', function (Dep) {

    return Dep.extend({

        getComposeEmailAttributes: function (scope, data, callback) {
            data = data || {};
            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

            Dep.prototype.getComposeEmailAttributes.call(this, scope, data, function (attributes) {
                attributes.name = '[#' + this.model.get('number') + '] ' + this.model.get('name');

                this.ajaxGetRequest('Case/action/emailAddressList?id=' + this.model.id).then(function (list) {
                    attributes.to = '';
                    attributes.cc = '';
                    attributes.nameHash = {};

                    list.forEach(function (item, i) {
                        if (i === 0) {
                            attributes.to += item.emailAddress + ';';
                        } else {
                            attributes.cc += item.emailAddress + ';';
                        }
                        attributes.nameHash[item.emailAddress] = item.name;
                    });
                    Espo.Ui.notify(false);

                    callback.call(this, attributes);

                }.bind(this));
            }.bind(this))
        }

    });
});
