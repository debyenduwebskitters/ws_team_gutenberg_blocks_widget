( function( blocks, blockEditor, components, element, i18n, serverSideRender ) {
	const el = element.createElement;
	const __ = i18n.__;
	const InspectorControls = blockEditor.InspectorControls;
	const MediaUpload = blockEditor.MediaUpload;
	const MediaUploadCheck = blockEditor.MediaUploadCheck;
	const useBlockProps = blockEditor.useBlockProps;
	const PanelBody = components.PanelBody;
	const RangeControl = components.RangeControl;
	const SelectControl = components.SelectControl;
	const TextControl = components.TextControl;
	const TextareaControl = components.TextareaControl;
	const ToggleControl = components.ToggleControl;
	const Button = components.Button;
	const ColorPalette = components.ColorPalette;
	const ServerSideRender = serverSideRender;

	function createDefaultMember() {
		return {
			name: '',
			designation: '',
			department: '',
			shortBio: '',
			profileImage: '',
			socialLinks: [ { network: '', url: '' } ],
			experience: '',
			buttonText: '',
			buttonUrl: ''
		};
	}

	function updateMember( members, index, key, value ) {
		return members.map( function( member, memberIndex ) {
			if ( memberIndex !== index ) {
				return member;
			}

			return Object.assign( {}, member, { [ key ]: value } );
		} );
	}

	function updateSocialLink( members, memberIndex, socialIndex, key, value ) {
		return members.map( function( member, currentIndex ) {
			if ( currentIndex !== memberIndex ) {
				return member;
			}

			const socialLinks = ( member.socialLinks || [] ).map( function( socialLink, currentSocialIndex ) {
				if ( currentSocialIndex !== socialIndex ) {
					return socialLink;
				}

				return Object.assign( {}, socialLink, { [ key ]: value } );
			} );

			return Object.assign( {}, member, { socialLinks: socialLinks } );
		} );
	}

	blocks.registerBlockType( 'blockforge/team-showcase', {
		edit: function( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const members = attributes.teamMembers || [];
			const blockProps = useBlockProps( {
				className: 'bfts-editor-shell'
			} );

			function setMemberField( index, key, value ) {
				setAttributes( { teamMembers: updateMember( members, index, key, value ) } );
			}

			function addMember() {
				setAttributes( { teamMembers: members.concat( [ createDefaultMember() ] ) } );
			}

			function removeMember( index ) {
				setAttributes( {
					teamMembers: members.filter( function( member, memberIndex ) {
						return memberIndex !== index;
					} )
				} );
			}

			function addSocialLink( index ) {
				const updated = members.map( function( member, memberIndex ) {
					if ( memberIndex !== index ) {
						return member;
					}

					return Object.assign( {}, member, {
						socialLinks: ( member.socialLinks || [] ).concat( [ { network: '', url: '' } ] )
					} );
				} );

				setAttributes( { teamMembers: updated } );
			}

			function removeSocialLink( memberIndex, socialIndex ) {
				const updated = members.map( function( member, currentIndex ) {
					if ( currentIndex !== memberIndex ) {
						return member;
					}

					return Object.assign( {}, member, {
						socialLinks: ( member.socialLinks || [] ).filter( function( socialLink, currentSocialIndex ) {
							return currentSocialIndex !== socialIndex;
						} )
					} );
				} );

				setAttributes( { teamMembers: updated } );
			}

			return el(
				element.Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Content Controls', 'blockforge-team-showcase' ), initialOpen: true },
						members.length ? members.map( function( member, index ) {
							return el(
								'div',
								{ className: 'bfts-editor-member', key: 'member-' + index },
								el( 'h3', null, __( 'Member', 'blockforge-team-showcase' ) + ' ' + ( index + 1 ) ),
								el( TextControl, {
									label: __( 'Name', 'blockforge-team-showcase' ),
									value: member.name || '',
									onChange: function( value ) { setMemberField( index, 'name', value ); }
								} ),
								el( TextControl, {
									label: __( 'Designation', 'blockforge-team-showcase' ),
									value: member.designation || '',
									onChange: function( value ) { setMemberField( index, 'designation', value ); }
								} ),
								el( TextControl, {
									label: __( 'Department', 'blockforge-team-showcase' ),
									value: member.department || '',
									onChange: function( value ) { setMemberField( index, 'department', value ); }
								} ),
								el( TextareaControl, {
									label: __( 'Short Bio', 'blockforge-team-showcase' ),
									value: member.shortBio || '',
									onChange: function( value ) { setMemberField( index, 'shortBio', value ); }
								} ),
								el( TextControl, {
									label: __( 'Experience', 'blockforge-team-showcase' ),
									value: member.experience || '',
									onChange: function( value ) { setMemberField( index, 'experience', value ); }
								} ),
								el( TextControl, {
									label: __( 'Button Text', 'blockforge-team-showcase' ),
									value: member.buttonText || '',
									onChange: function( value ) { setMemberField( index, 'buttonText', value ); }
								} ),
								el( TextControl, {
									label: __( 'Button URL', 'blockforge-team-showcase' ),
									value: member.buttonUrl || '',
									onChange: function( value ) { setMemberField( index, 'buttonUrl', value ); }
								} ),
								el(
									MediaUploadCheck,
									null,
									el( MediaUpload, {
										onSelect: function( media ) { setMemberField( index, 'profileImage', media && media.url ? media.url : '' ); },
										allowedTypes: [ 'image' ],
										render: function( mediaProps ) {
											return el(
												Button,
												{ onClick: mediaProps.open, variant: 'secondary' },
												member.profileImage ? __( 'Replace Profile Image', 'blockforge-team-showcase' ) : __( 'Upload Profile Image', 'blockforge-team-showcase' )
											);
										}
									} )
								),
								el( 'div', { className: 'bfts-editor-social-wrap' },
									( member.socialLinks || [] ).map( function( socialLink, socialIndex ) {
										return el(
											'div',
											{ className: 'bfts-editor-social-row', key: 'social-' + socialIndex },
											el( TextControl, {
												label: __( 'Social Network', 'blockforge-team-showcase' ),
												value: socialLink.network || '',
												onChange: function( value ) {
													setAttributes( { teamMembers: updateSocialLink( members, index, socialIndex, 'network', value ) } );
												}
											} ),
											el( TextControl, {
												label: __( 'Social URL', 'blockforge-team-showcase' ),
												value: socialLink.url || '',
												onChange: function( value ) {
													setAttributes( { teamMembers: updateSocialLink( members, index, socialIndex, 'url', value ) } );
												}
											} ),
											el( Button, {
												variant: 'link',
												isDestructive: true,
												onClick: function() { removeSocialLink( index, socialIndex ); }
											}, __( 'Remove Social Link', 'blockforge-team-showcase' ) )
										);
									} )
								),
								el( Button, {
									variant: 'secondary',
									onClick: function() { addSocialLink( index ); }
								}, __( 'Add Social Link', 'blockforge-team-showcase' ) ),
								el( Button, {
									variant: 'link',
									isDestructive: true,
									onClick: function() { removeMember( index ); }
								}, __( 'Remove Member', 'blockforge-team-showcase' ) )
							);
						} ) : el(
							'div',
							{ className: 'bfts-editor-member bfts-editor-member--empty' },
							el( 'p', null, __( 'No team members added yet. Use the button below or the canvas action to start.', 'blockforge-team-showcase' ) )
						),
						el( Button, { variant: 'primary', onClick: addMember }, __( 'Add Team Member', 'blockforge-team-showcase' ) )
					),
					el(
						PanelBody,
						{ title: __( 'Layout Controls', 'blockforge-team-showcase' ), initialOpen: false },
						el( RangeControl, {
							label: __( 'Grid Columns', 'blockforge-team-showcase' ),
							min: 1,
							max: 4,
							value: attributes.gridColumns,
							onChange: function( value ) { setAttributes( { gridColumns: value } ); }
						} ),
						el( TextControl, {
							label: __( 'Show Items', 'blockforge-team-showcase' ),
							type: 'number',
							min: 1,
							max: 24,
							value: attributes.itemsPerPage || 9,
							onChange: function( value ) {
								const parsedValue = parseInt( value, 10 );
								setAttributes( { itemsPerPage: parsedValue > 0 ? parsedValue : 1 } );
							}
						} ),
						el( SelectControl, {
							label: __( 'Card Alignment', 'blockforge-team-showcase' ),
							value: attributes.cardAlignment,
							options: [
								{ label: __( 'Left', 'blockforge-team-showcase' ), value: 'left' },
								{ label: __( 'Center', 'blockforge-team-showcase' ), value: 'center' },
								{ label: __( 'Right', 'blockforge-team-showcase' ), value: 'right' }
							],
							onChange: function( value ) { setAttributes( { cardAlignment: value } ); }
						} ),
						el( TextControl, {
							label: __( 'Card Height', 'blockforge-team-showcase' ),
							help: __( 'Use CSS values like 100%, 420px, or auto.', 'blockforge-team-showcase' ),
							value: attributes.cardHeight,
							onChange: function( value ) { setAttributes( { cardHeight: value } ); }
						} ),
						el( SelectControl, {
							label: __( 'Image Position', 'blockforge-team-showcase' ),
							value: attributes.imagePosition,
							options: [
								{ label: __( 'Top', 'blockforge-team-showcase' ), value: 'top' },
								{ label: __( 'Left', 'blockforge-team-showcase' ), value: 'left' },
								{ label: __( 'Right', 'blockforge-team-showcase' ), value: 'right' }
							],
							onChange: function( value ) { setAttributes( { imagePosition: value } ); }
						} ),
						el( ToggleControl, {
							label: __( 'Popup Toggle', 'blockforge-team-showcase' ),
							checked: !! attributes.popupToggle,
							onChange: function( value ) { setAttributes( { popupToggle: value } ); }
						} )
					),
					el(
						PanelBody,
						{ title: __( 'Style Controls', 'blockforge-team-showcase' ), initialOpen: false },
						el( 'p', null, __( 'Card Background', 'blockforge-team-showcase' ) ),
						el( ColorPalette, {
							value: attributes.cardBackground,
							onChange: function( value ) { setAttributes( { cardBackground: value || '#ffffff' } ); }
						} ),
						el( RangeControl, {
							label: __( 'Border Radius', 'blockforge-team-showcase' ),
							min: 0,
							max: 50,
							value: attributes.borderRadius,
							onChange: function( value ) { setAttributes( { borderRadius: value } ); }
						} ),
						el( RangeControl, {
							label: __( 'Typography Font Size', 'blockforge-team-showcase' ),
							min: 12,
							max: 28,
							value: attributes.typographyFontSize,
							onChange: function( value ) { setAttributes( { typographyFontSize: value } ); }
						} ),
						el( TextControl, {
							label: __( 'Typography Line Height', 'blockforge-team-showcase' ),
							value: attributes.typographyLineHeight,
							onChange: function( value ) { setAttributes( { typographyLineHeight: value } ); }
						} ),
						el( SelectControl, {
							label: __( 'Hover Effects', 'blockforge-team-showcase' ),
							value: attributes.hoverEffect,
							options: [
								{ label: __( 'Lift', 'blockforge-team-showcase' ), value: 'lift' },
								{ label: __( 'Glow', 'blockforge-team-showcase' ), value: 'glow' },
								{ label: __( 'Tilt', 'blockforge-team-showcase' ), value: 'tilt' }
							],
							onChange: function( value ) { setAttributes( { hoverEffect: value } ); }
						} ),
						el( 'p', null, __( 'Button Background', 'blockforge-team-showcase' ) ),
						el( ColorPalette, {
							value: attributes.buttonBackground,
							onChange: function( value ) { setAttributes( { buttonBackground: value || '#111827' } ); }
						} ),
						el( 'p', null, __( 'Button Text Color', 'blockforge-team-showcase' ) ),
						el( ColorPalette, {
							value: attributes.buttonTextColor,
							onChange: function( value ) { setAttributes( { buttonTextColor: value || '#ffffff' } ); }
						} ),
						el( ToggleControl, {
							label: __( 'Dark Mode Toggle', 'blockforge-team-showcase' ),
							checked: !! attributes.darkModeToggle,
							onChange: function( value ) { setAttributes( { darkModeToggle: value } ); }
						} ),
						el( TextControl, {
							label: __( 'ACF Field Group Key', 'blockforge-team-showcase' ),
							help: __( 'Optional. Use this to document the paired ACF group for editorial workflows.', 'blockforge-team-showcase' ),
							value: attributes.acfFieldGroup,
							onChange: function( value ) { setAttributes( { acfFieldGroup: value } ); }
						} )
					)
				),
				el(
					'div',
					blockProps,
					members.length ? el(
						'div',
						{ className: 'bfts-editor-preview' },
						el( ServerSideRender, {
							block: 'blockforge/team-showcase',
							attributes: attributes
						} )
					) : el(
						'div',
						{ className: 'bfts-editor-preview bfts-editor-preview--empty' },
						el( 'h3', null, __( 'WS Team Block View(Debyendu)', 'blockforge-team-showcase' ) ),
						el( 'p', null, __( 'Select this block, open the right sidebar, and click "Add Team Member" to start building the team list.', 'blockforge-team-showcase' ) ),
						el( Button, { variant: 'primary', onClick: addMember }, __( 'Add Team Member', 'blockforge-team-showcase' ) )
					)
				)
			);
		},
		save: function() {
			return null;
		}
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n, window.wp.serverSideRender );
