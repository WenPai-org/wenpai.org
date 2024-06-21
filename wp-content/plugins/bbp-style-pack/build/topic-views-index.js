( function ( blocks, generator ) {
    var defs = [
	    {
	        "title": "(Style Pack) Topic Views List",
	        "status": "publish",
	        "namespace": "bbp-style-pack",
	        "slug": "topic-views-list-widget",
	        "modified": 1680629415,
	        "data": {
	            "isDynamic": true,
	            "icon": "",
	            "description": "A list of registered optional topic views.",
	            "keywords": "",
	            "parent": [],
	            "ancestor": [],
	            "category": "widgets",
	            "wbbApiVersion": 1,
	            "apiVersion": 2,
	            "attributes": [
	                {
	                    "id": "lg2imvnn",
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
	                    "id": "lws263l4",
	                    "name": "bbpressOnly",
	                    "parent": "",
	                    "type": "boolean",
	                    "source": "block",
	                    "selector": "",
	                    "attribute": "",
	                    "default": "",
	                    "order": 1
	                }
	            ],
	            "sidebar": {
	                "nodes": [
	                    {
	                        "parent": "",
	                        "name": "div",
	                        "type": "element",
	                        "order": 0,
	                        "id": "lg2in7z6"
	                    },
						{
	                        "attribute": "bbpressOnly",
	                        "label": "Show only on bbPress Pages",
	                        "help": "",
	                        "checked": false,
	                        "classes": [],
	                        "parent": "lg2in7z6",
	                        "name": "Toggle",
	                        "type": "component",
	                        "order": 1,
	                        "id": "lfasatgx"
	                    },
	                    {
	                        "attribute": "title",
	                        "label": "Title:",
	                        "placeholder": "",
	                        "parent": "lg2in7z6",
	                        "name": "TextControl",
	                        "type": "component",
	                        "order": 2,
	                        "id": "lg2incyo"
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
	                        "id": "lg2janl9"
	                    },
	                    {
	                        "parent": "lg2janl9",
	                        "name": "ul",
	                        "type": "element",
	                        "order": 1,
	                        "id": "lg2jaw1n",
	                        "content": "(Style Pack) Topic Views List Widget	",
							"classes": [
	                            {
	                                "id": "lfyg8h32",
	                                "name": "bsp-widget-heading"
	                            }
	                        ],
	                    },
	                    {
	                        "parent": "lg2janl9",
	                        "name": "ul",
	                        "type": "element",
	                        "order": 2,
	                        "id": "lg2jaxmn",
	                        "content": "Click here for settings on right hand side",
							"classes": [
	                            {
	                                "id": "lssg8h32",
	                                "name": "bsp-widget-settings"
	                            }
	                        ],
	                    },
						{
							"parent": "lg2janl9",
							"name": "DynamicPreview",
							"type": "component",
							"order": 3,
							"id": "lg1dblzk"
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