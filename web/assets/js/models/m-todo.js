toDo = {
    models: {},
    collections: {},
    views: {}
};

//-- todo model is the intitial toDoNote object
toDo.models.toDoNote = Backbone.Model.extend({
    defaults: {
        'id': {
            'date': '',
            'client': '',
            'note': '',
            'tags': ''
        }
    }
});

//-- create collections
toDo.collections.toDoNotes = Backbone.Collection.extend({
    model: toDo.models.toDoNote, // creates model
    url: "/todo-json",
    initialize: function() {
    }
});
