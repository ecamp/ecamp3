
jQuery(function($){

    var events = $(CNS('ecamp.events'));

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
            events.trigger('membership.requests.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('membership.requests.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('membership.requests.changed.post');
        });

        ctrl();
    }

    function revokeRequest(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('membership.requests.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('membership.requests.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('membership.requests.changed.post');
        });

        ctrl();
    }

    function rejectRequest(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('membership.requests.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('membership.requests.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('membership.requests.changed.post');
        });

        ctrl();
    }

    function acceptRequest(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('membership.members.changed.pre');
            events.trigger('membership.requests.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('membership.members.changed');
            events.trigger('membership.requests.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('membership.members.changed.post');
            events.trigger('membership.requests.changed.post');
        });

        ctrl();
    }


    function inviteUser(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('membership.invitations.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('membership.invitations.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('membership.invitations.changed.post');
        });

        ctrl();
    }

    function revokeInvitation(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('membership.invitations.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('membership.invitations.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('membership.invitations.changed.post');
        });

        ctrl();
    }

    function rejectInvitation(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('membership.invitations.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('membership.invitations.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('membership.invitations.changed.post');
        });

        ctrl();
    }

    function acceptInvitation(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('membership.invitations.changed.pre');
            events.trigger('membership.members.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('membership.invitations.changed');
            events.trigger('membership.members.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('membership.invitations.changed.post');
            events.trigger('membership.members.changed.post');
        });

        ctrl();
    }


    function leaveGroup(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('membership.members.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('membership.members.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('membership.members.changed.post');
        });

        ctrl();
    }

    function kickUser(event){

        var ctrl = getMembershipController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('membership.members.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('membership.members.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('membership.members.changed.post');
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
