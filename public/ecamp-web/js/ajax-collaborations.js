
jQuery(function($){

    var events = $(CNS('ecamp.events'));

    function getCollaborationController(event){
        event.preventDefault();

        var $this = $(this);
        var url = $this.attr('href');
        var container = $this.closest('div.edit-collaboration-container');

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


    function requestCollaboration(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('collaboration.requests.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('collaboration.requests.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('collaboration.requests.changed.post');
        });

        ctrl();
    }

    function revokeRequest(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('collaboration.requests.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('collaboration.requests.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('collaboration.requests.changed.post');
        });

        ctrl();
    }

    function rejectRequest(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('collaboration.requests.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('collaboration.requests.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('collaboration.requests.changed.post');
        });

        ctrl();
    }

    function acceptRequest(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('collaboration.collaborators.changed.pre');
            events.trigger('collaboration.requests.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('collaboration.collaborators.changed');
            events.trigger('collaboration.requests.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('collaboration.collaborators.changed.post');
            events.trigger('collaboration.requests.changed.post');
        });

        ctrl();
    }


    function inviteUser(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('collaboration.invitations.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('collaboration.invitations.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('collaboration.invitations.changed.post');
        });

        ctrl();
    }

    function revokeInvitation(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('collaboration.invitations.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('collaboration.invitations.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('collaboration.invitations.changed.post');
        });

        ctrl();
    }

    function rejectInvitation(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('collaboration.invitations.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('collaboration.invitations.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('collaboration.invitations.changed.post');
        });

        ctrl();
    }

    function acceptInvitation(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('collaboration.invitations.changed.pre');
            events.trigger('collaboration.collaborators.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('collaboration.invitations.changed');
            events.trigger('collaboration.collaborators.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('collaboration.invitations.changed.post');
            events.trigger('collaboration.collaborators.changed.post');
        });

        ctrl();
    }


    function leaveCamp(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('collaboration.collaborators.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('collaboration.collaborators.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('collaboration.collaborators.changed.post');
        });

        ctrl();
    }

    function kickUser(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('collaboration.collaborators.changed.pre');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('collaboration.collaborators.changed');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('collaboration.collaborators.changed.post');
        });

        ctrl();
    }


    $(document).on('click', '.edit-collaboration-container a.request-collaboration', requestCollaboration);
    $(document).on('click', '.edit-collaboration-container a.revoke-request', revokeRequest);
    $(document).on('click', '.edit-collaboration-container a.reject-request', rejectRequest);
    $(document).on('click', '.edit-collaboration-container a.accept-request', acceptRequest);

    $(document).on('click', '.edit-collaboration-container a.invite-user', inviteUser);
    $(document).on('click', '.edit-collaboration-container a.revoke-invitation', revokeInvitation);
    $(document).on('click', '.edit-collaboration-container a.reject-invitation', rejectInvitation);
    $(document).on('click', '.edit-collaboration-container a.accept-invitation', acceptInvitation);

    $(document).on('click', '.edit-collaboration-container a.leave-camp', leaveCamp);
    $(document).on('click', '.edit-collaboration-container a.kick-user', kickUser);
});
