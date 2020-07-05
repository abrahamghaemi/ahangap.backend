

Espo.define('crm:views/knowledge-base-article/record/list-for-case', 'views/record/list', function (Dep) {

    return Dep.extend({

        actionSendInEmail: function (data) {
            var model = this.collection.get(data.id);

            var parentModel = this.getParentView().model;

            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));

            new Promise(
                function (resolve, reject) {
                    model.fetch().then(function () {
                        resolve();
                    });
                }
            ).then(function () {
                return new Promise(function (resolve, reject) {
                    if (parentModel.get('contactsIds') && parentModel.get('contactsIds').length) {
                        this.getCollectionFactory().create('Contact', function (contactList) {
                            var contactListFinal = [];
                            contactList.url = 'Case/' + parentModel.id + '/contacts';
                            contactList.fetch().then(function () {
                                contactList.forEach(function (contact) {
                                    if (contact.id == parentModel.get('contactId')) {
                                        contactListFinal.unshift(contact);
                                    } else {
                                        contactListFinal.push(contact);
                                    }
                                });
                                resolve(contactListFinal);
                            }, function () {resolve([])});
                        }, this);
                    } else if (parentModel.get('accountId')) {
                        this.getModelFactory().create('Account', function (account) {
                            account.id = parentModel.get('accountId');
                            account.fetch().then(function () {
                                resolve([account]);
                            }, function () {resolve([])});
                        }, this);
                    } else if (parentModel.get('leadId')) {
                        this.getModelFactory().create('Lead', function (account) {
                            lead.id = parentModel.get('leadId');
                            lead.fetch().then(function () {
                                resolve([lead]);
                            }, function () {resolve([])});
                        }, this);
                    } else {
                        resolve([]);
                    }
                }.bind(this))
            }.bind(this)).then(function (list) {
                var attributes = {
                    parentType: 'Case',
                    parentId: parentModel.id,
                    parentName: parentModel.get('name'),
                    name: '[#' + parentModel.get('number') + ']'
                };

                attributes.to = '';
                attributes.cc = '';
                attributes.nameHash = {};

                list.forEach(function (model, i) {
                    if (model.get('emailAddress')) {
                        if (i === 0) {
                            attributes.to += model.get('emailAddress') + ';';
                        } else {
                            attributes.cc += model.get('emailAddress') + ';';
                        }
                        attributes.nameHash[model.get('emailAddress')] = model.get('name');
                    }
                });

                Espo.require('crm:knowledge-base-helper', function (Helper) {
                    (new Helper(this.getLanguage())).getAttributesForEmail(model, attributes, function (attributes) {
                        var viewName = this.getMetadata().get('clientDefs.Email.modalViews.compose') || 'views/modals/compose-email';
                        this.createView('composeEmail', viewName, {
                            attributes: attributes,
                            selectTemplateDisabled: true,
                            signatureDisabled: true
                        }, function (view) {
                            Espo.Ui.notify(false);
                            view.render();

                            this.listenToOnce(view, 'after:send', function () {
                                parentModel.trigger('after:relate');
                            }, this);
                        }, this);
                    }.bind(this));
                }, this);
            }.bind(this)).catch(function () {
                Espo.Ui.notify(false);
            });
        }

    });
});
