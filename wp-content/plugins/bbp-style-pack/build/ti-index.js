( function ( blocks, generator ) {
    var defs = [
	    {
	        "title": "(Style Pack) Single Topic Information",
	        "status": "publish",
	        "namespace": "bbp-style-pack",
	        "slug": "bsp-single-topic-information",
	        "modified": 1680361254,
	        "data": {
	            "isDynamic": true,
	            "icon": "",
	            "description": "",
	            "keywords": "",
	            "parent": [],
	            "ancestor": [],
	            "category": "widgets",
	            "wbbApiVersion": 1,
	            "apiVersion": 2,
	            "attributes": [
	                {
	                    "id": "lfsqjf13",
	                    "name": "title",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "Topic Information",
	                    "order": 0
	                },
	                {
	                    "id": "lfsqjtyp",
	                    "name": "in",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "In ",
	                    "order": 1
	                },
	                {
	                    "id": "lfsqjufn",
	                    "name": "reply",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "Reply",
	                    "order": 2
	                },
	                {
	                    "id": "lfsqjuls",
	                    "name": "replies",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "Replies",
	                    "order": 3
	                },
	                {
	                    "id": "lfsqjuqv",
	                    "name": "participant",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "Participant",
	                    "order": 4
	                },
	                {
	                    "id": "lfsqjuww",
	                    "name": "participants",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "Participants",
	                    "order": 5
	                },
	                {
	                    "id": "lfsqju9z",
	                    "name": "last_activity",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "Last Activity: ",
	                    "order": 7
	                },
	                {
	                    "id": "lfsqju4g",
	                    "name": "last_reply",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "Last Reply: ",
	                    "default": "",
	                    "order": 6
	                },
	                {
	                    "id": "lfsr27us",
	                    "name": "show_icons",
	                    "parent": "",
	                    "type": "boolean",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 8
	                }
	            ],
	            "sidebar": {
	                "nodes": [
	                    {
	                        "parent": "",
	                        "name": "div",
	                        "type": "element",
	                        "order": 0,
	                        "id": "lfsr2xeb"
	                    },
	                    {
	                        "attribute": "title",
	                        "label": "Title:",
	                        "placeholder": "Topic Information",
	                        "parent": "",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 1,
	                        "id": "lfsr34ib"
	                    },
	                    {
	                        "attribute": "show_icons",
	                        "label": "Show Icons",
	                        "help": "",
	                        "checked": false,
	                        "classes": [],
	                        "parent": "",
	                        "name": "Toggle",
	                        "type": "component",
	                        "order": 2,
	                        "id": "lfsr47cw"
	                    },
	                    {
	                        "attribute": "in",
	                        "label": "In: ",
	                        "placeholder": "In ",
	                        "parent": "",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 3,
	                        "id": "lfsr4p68"
	                    },
	                    {
	                        "attribute": "reply",
	                        "label": "Reply:",
	                        "placeholder": "Reply",
	                        "parent": "",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 4,
	                        "id": "lfsr5fmc"
	                    },
	                    {
	                        "attribute": "replies",
	                        "label": "Replies:",
	                        "placeholder": "Replies",
	                        "parent": "",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 5,
	                        "id": "lfsr63sy"
	                    },
	                    {
	                        "attribute": "participant",
	                        "label": "Participant:",
	                        "placeholder": "Participant",
	                        "parent": "",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 6,
	                        "id": "lfsr6iia"
	                    },
	                    {
	                        "attribute": "participants",
	                        "label": "Participants:",
	                        "placeholder": "Participants",
	                        "parent": "",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 7,
	                        "id": "lfsr74rq"
	                    },
	                    {
	                        "attribute": "last_reply",
	                        "label": "Last Reply:",
	                        "placeholder": "Last Reply:",
	                        "parent": "",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 8,
	                        "id": "lfsr7efn"
	                    },
	                    {
	                        "attribute": "last_activity",
	                        "label": "Last Activity:",
	                        "placeholder": "Last Activity:",
	                        "parent": "",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 9,
	                        "id": "lfsr84wa"
	                    }
	                ]
	            },
	            "edit": {
	                "nodes": [
	                    {
	                        "parent": "",
	                        "name": "div",
	                        "type": "element",
	                        "order": 0,
	                        "id": "lfsrbunz"
	                    },
						{
	                        "parent": "lfsrbunz",
	                        "name": "ul",
	                        "type": "element",
	                        "order": 1,
	                        "id": "lfp6zhjw",
	                        "content": "(Style Pack) Single Topic Information Widget",
	                        "classes": [
	                            {
	                                "id": "lfyg8h32",
	                                "name": "bsp-widget-heading"
	                            }
	                        ],
						},
	                    {
	                        "parent": "lfsrbunz",
	                        "name": "ul",
	                        "type": "element",
	                        "order": 1,
	                        "id": "lfsrbwzi",
	                        "content": "Click here for settings on right hand side",
							"classes": [
	                            {
	                                "id": "lfeg8h32",
	                                "name": "bsp-widget-settings"
	                            }
	                        ],
	                    },
	                    {
	                        "parent": "lfsrbunz",
	                        "name": "ul",
	                        "type": "element",
	                        "order": 2,
	                        "id": "lfy3axl6",
	                        "content": "This widget will only show in single topics",
							"classes": [
	                            {
	                                "id": "lfsg8h32",
	                                "name": "bsp-widget-settings"
	                            }
	                        ],
	                    }
	                ]
	            },
	            "save": {
	                "nodes": []
	            },
	            "supports": {
	                "align": [],
	                "anchor": false,
	                "customClassName": true,
	                "multiple": true,
	                "inserter": true,
	                "typography": {
	                    "fontSize": false,
	                    "lineHeight": false
	                },
	                "spacing": {
	                    "margin": false,
	                    "padding": false
	                },
	                "color": false
	            }
	        },
	        "css": "",
	        "versions": [
	            
	        ]
	    }
	];

    defs.forEach( function( def ){
		var name = def.namespace + '/' + def.slug;

		if ( ! blocks.getBlockType( name ) ) {
			var block = generator.makeBlock( def );

			blocks.registerBlockType( name, block );
		}
    } );
} )( window.wp.blocks, window.wickedBlockBuilder.generator.v1 );