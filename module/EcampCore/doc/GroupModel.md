![Object Model Group](http://yuml.me/diagram/scruffy/class/
[Group]<>*-parent 0..1>[Group],
[Group]++-0..1>[Image],
[GroupMembership]<>*-1>[Group],
[User]<1-*<>[GroupMembership],
[Group]++1-*>[Camp],
)