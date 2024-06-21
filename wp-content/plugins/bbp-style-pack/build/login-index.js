( function ( blocks, generator ) {
    var defs = [
	    {
	        "title": "(Style Pack) Login Widget",
	        "status": "publish",
	        "slug": "bsp-login-widget",
			"namespace": "bbp-style-pack",
	        "modified": 1679822679,
	        "data": {
	            "isDynamic": true,
	            "icon": "",
	            "description": "Adds a Login Widget with optional links to sign-up and lost password pages.",
	            "keywords": "",
	            "parent": [],
	            "ancestor": [],
	            "category": "widgets",
	            "wbbApiVersion": 1,
	            "apiVersion": 2,
	            "attributes": [
	                {
	                    "id": "lfobg3iu",
	                    "name": "title",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 0
	                },
	                {
	                    "id": "lfobg3wg",
	                    "name": "register",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 1
	                },
	                {
	                    "id": "lfobg462",
	                    "name": "lostpass",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 2
	                },
	                {
	                    "id": "lfojsdf3",
	                    "name": "helptext",
	                    "parent": "",
	                    "type": "string",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 3
	                },
					{
	                    "id": "lws263l4",
	                    "name": "bbpressOnly",
	                    "parent": "",
	                    "type": "boolean",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 2
	                }
	            ],
	            "sidebar": {
	                "nodes": [
	                    {
	                        "parent": "",
	                        "name": "div",
	                        "type": "element",
	                        "order": 0,
	                        "id": "lfobh065"
	                    },
						{
	                        "attribute": "bbpressOnly",
	                        "label": "Show only on bbPress Pages",
	                        "help": "",
	                        "checked": false,
	                        "classes": [],
	                        "parent": "lfobh065",
	                        "name": "Toggle",
	                        "type": "component",
	                        "order": 1,
	                        "id": "lfasatgx"
	                    },
	                    {
	                        "attribute": "title",
	                        "label": "Title:",
	                        "placeholder": "",
	                        "parent": "lfobh065",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 2,
	                        "id": "lfobh2ot"
	                    },
	                    {
	                        "attribute": "register",
	                        "label": "Register URI:",
	                        "placeholder": "",
	                        "parent": "lfobh065",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 3,
	                        "id": "lfojrakz"
	                    },
	                    {
	                        "attribute": "lostpass",
	                        "label": "Lost Password URI:",
	                        "placeholder": "",
	                        "parent": "lfobh065",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 4,
	                        "id": "lfojrb1i"
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
	                        "id": "lfojs3a0"
	                    },
						{
	                        "parent": "lfojs3a0",
	                        "name": "ul",
	                        "type": "element",
	                        "order": 1,
	                        "id": "lfp6zhj3",
	                        "content": "(Style Pack) Login Widget",
	                        "classes": [
	                            {
	                                "id": "lfyg8h6j",
	                                "name": "bsp-widget-heading"
	                            }
	                        ],
						},
	                    {
	                        "parent": "lfojs3a0",
	                        "name": "DynamicPreview",
	                        "type": "component",
	                        "order": 3,
	                        "id": "lfp6wt5w"
	                    },
	                    {
	                        "parent": "lfojs3a0",
	                        "name": "ul",
	                        "type": "element",
	                        "order": 2,
	                        "id": "lfp6zhj3",
	                        "content": "Click here for settings on right hand side",
							"classes": [
	                            {
	                                "id": "lqg8h32",
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