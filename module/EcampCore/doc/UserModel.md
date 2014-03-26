![Object Model User](http://yuml.me/diagram/scruffy/class/
[User]<>-0..1>[Image],
[User]++owner 1-myCamps *>[Camp],
[User]++1-0..1>[Login],
[User]< 1- *<>[UserRelationship],
[User2]< 1- *<>[UserRelationship],
[UserRelationship]-counterpart 1>[UserRelationship],
[User2]-[note: User2 is the same class as User but renamed in this diagram for better visibility.{bg:cornsilk}],
[User]<1-*<>[CampCollaboration],
[CampCollaboration]<>*-1>[Camp],
[User]<creator 1-[Camp],
)