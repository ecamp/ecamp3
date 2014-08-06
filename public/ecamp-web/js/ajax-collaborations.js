
jQuery(function($){

    var events = CNS('ecamp.events');

    function getCollaborationController(event){
        event.preventDefault();

        var $this = $(this);
        var url = $this.attr('href');
        var container = $this.closest('div.edit-collaboration-container');

        var result = function(){
            result.fadeOutPromis.resolve();
            container.fadeOut();
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
            events.trigger('camp.collaborationRequests.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('camp.collaborationRequests.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('camp.collaborationRequests.endUpdate');
        });

        ctrl();
    }

    function revokeRequest(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('camp.collaborationRequests.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('camp.collaborationRequests.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('camp.collaborationRequests.endUpdate');
        });

        ctrl();
    }

    function rejectRequest(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('camp.collaborationRequests.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('camp.collaborationRequests.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('camp.collaborationRequests.endUpdate');
        });

        ctrl();
    }

    function acceptRequest(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('camp.collaborations.beginUpdate');
            events.trigger('camp.collaborationRequests.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('camp.collaborations.refresh');
            events.trigger('camp.collaborationRequests.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('camp.collaborations.endUpdate');
            events.trigger('camp.collaborationRequests.endUpdate');
        });

        ctrl();
    }


    function inviteUser(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('camp.collaborationInvitations.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('camp.collaborationInvitations.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('camp.collaborationInvitations.endUpdate');
        });

        ctrl();
    }

    function revokeInvitation(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('camp.collaborationInvitations.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('camp.collaborationInvitations.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('camp.collaborationInvitations.endUpdate');
        });

        ctrl();
    }

    function rejectInvitation(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('camp.collaborationInvitations.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('camp.collaborationInvitations.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('camp.collaborationInvitations.endUpdate');
        });

        ctrl();
    }

    function acceptInvitation(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('camp.collaborations.beginUpdate');
            events.trigger('camp.collaborationInvitations.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('camp.collaborations.refresh');
            events.trigger('camp.collaborationInvitations.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('camp.collaborations.endUpdate');
            events.trigger('camp.collaborationInvitations.endUpdate');
        });

        ctrl();
    }


    function leaveCamp(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('camp.collaborations.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('camp.collaborations.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('camp.collaborations.endUpdate');
        });

        ctrl();
    }

    function kickUser(event){

        var ctrl = getCollaborationController.call(this, event);
        ctrl.fadeOutPromis.then(function(){
            events.trigger('camp.collaborations.beginUpdate');
        });
        ctrl.loadPromis.then(function(){
            events.trigger('camp.collaborations.refresh');
        });
        ctrl.fadeInPromis.then(function(){
            events.trigger('camp.collaborations.endUpdate');
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
