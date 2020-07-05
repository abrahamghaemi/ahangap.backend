

 Espo.define('crm:views/call/detail', ['views/detail', 'crm:views/meeting/detail'], function (Dep, MeetingDetail) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            this.controlSendInvitationsButton();
            this.controlAcceptanceStatusButton();

            this.listenTo(this.model, 'sync', function () {
                this.controlSendInvitationsButton();
            }, this);

            this.listenTo(this.model, 'sync', function () {
                this.controlAcceptanceStatusButton();
            }, this);
        },

        actionSendInvitations: function () {
            MeetingDetail.prototype.actionSendInvitations.call(this);
        },

        actionSetAcceptanceStatus: function () {
            MeetingDetail.prototype.actionSetAcceptanceStatus.call(this);
        },

        controlSendInvitationsButton: function () {
            MeetingDetail.prototype.controlSendInvitationsButton.call(this);
        },

        controlAcceptanceStatusButton: function () {
            MeetingDetail.prototype.controlAcceptanceStatusButton.call(this);
        },

    });
});
