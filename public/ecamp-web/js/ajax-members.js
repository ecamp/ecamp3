
jQuery(function($){

    var events = CNS('ecamp.events');

    function getMembershipController(event){
        event.preventDefault();

        var $this = $(this);
        var url = $this.attr('href');
        var container = $this.closest('div.edit-membership-container');

        var result = function(){
            container.fadeOut(result.fadeOutPromis.resolve);
            container.load(url, result.loadPromis.resolve);
        };
        result.fadeOutPromis = $.Deferred();
        result.loadPromis = $.Deferred();
        result.fadeInPromis = $.Deferred();

        result.loadPromis.then(function(){
            container.fadeIn(result.fadeInPromis.resolve);
        });

        return result;
    }


    function requestMembership(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('group.membershipRequests.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('group.membershipRequests.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('group.membershipRequests.endUpdate');
        });

        ctrl();
    }

    function revokeRequest(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('group.membershipRequests.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('group.membershipRequests.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('group.membershipRequests.endUpdate');
        });

        ctrl();
    }

    function rejectRequest(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('group.membershipRequests.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('group.membershipRequests.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('group.membershipRequests.endUpdate');
        });

        ctrl();
    }

    function acceptRequest(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('group.memberships.beginUpdate');
            events.trigger('group.membershipRequests.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('group.memberships.refresh');
            events.trigger('group.membershipRequests.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('group.memberships.endUpdate');
            events.trigger('group.membershipRequests.endUpdate');
        });

        ctrl();
    }


    function inviteUser(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('group.membershipInvitations.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('group.membershipInvitations.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('group.membershipInvitations.endUpdate');
        });

        ctrl();
    }

    function revokeInvitation(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('group.membershipInvitations.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('group.membershipInvitations.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('group.membershipInvitations.endUpdate');
        });

        ctrl();
    }

    function rejectInvitation(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('group.membershipInvitations.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('group.membershipInvitations.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('group.membershipInvitations.endUpdate');
        });

        ctrl();
    }

    function acceptInvitation(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('group.membershipInvitations.beginUpdate');
            events.trigger('group.memberships.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('group.membershipInvitations.refresh');
            events.trigger('group.memberships.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('group.membershipInvitations.endUpdate');
            events.trigger('group.memberships.endUpdate');
        });

        ctrl();
    }


    function leaveGroup(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('group.memberships.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('group.memberships.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('group.memberships.endUpdate');
        });

        ctrl();
    }

    function kickUser(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('group.memberships.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('group.memberships.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('group.memberships.endUpdate');
        });

        ctrl();
    }


    $(document).on('click', '.edit-membership-container a.request-membership', requestMembership);
    $(document).on('click', '.edit-membership-container a.revoke-request', revokeRequest);
    $(document).on('click', '.edit-membership-container a.reject-request', rejectRequest);
    $(document).on('click', '.edit-membership-container a.accept-request', acceptRequest);

    $(document).on('click', '.edit-membership-container a.invite-user', inviteUser);
    $(document).on('click', '.edit-membership-container a.revoke-invitation', revokeInvitation);
    $(document).on('click', '.edit-membership-container a.reject-invitation', rejectInvitation);
    $(document).on('click', '.edit-membership-container a.accept-invitation', acceptInvitation);

    $(document).on('click', '.edit-membership-container a.leave-group', leaveGroup);
    $(document).on('click', '.edit-membership-container a.kick-user', kickUser);
});
