

Espo.define('crm:views/lead/record/panels/converted-to', 'views/record/panels/side', function (Dep) {

    return Dep.extend({

        setupFields: function () {
            this.fieldList = [];

            if (this.getAcl().check('Account') && !this.getMetadata().get('scopes.Account.disabled')) {
                this.fieldList.push('createdAccount');
            }

            if (this.getAcl().check('Contact') && !this.getMetadata().get('scopes.Contact.disabled')) {
                this.fieldList.push('createdContact');
            }
            if (this.getAcl().check('Opportunity') && !this.getMetadata().get('scopes.Opportunity.disabled')) {
                this.fieldList.push('createdOpportunity');
            }
        }

    });

});
