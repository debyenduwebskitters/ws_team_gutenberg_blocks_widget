( function() {
	function escapeHtml( value ) {
		return String( value || '' )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' )
			.replace( /"/g, '&quot;' )
			.replace( /'/g, '&#039;' );
	}

	function safeUrl( value ) {
		try {
			const url = new window.URL( value, window.location.origin );
			return [ 'http:', 'https:', 'mailto:', 'tel:' ].includes( url.protocol ) ? url.href : '';
		} catch ( error ) {
			return '';
		}
	}

	function getSocialIcon( network ) {
		switch ( getSocialNetworkKey( network ) ) {
			case 'facebook':
				return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M13.5 22v-8h2.7l.4-3h-3.1V9.1c0-.9.3-1.6 1.7-1.6h1.5V4.8c-.3 0-1.1-.1-2.1-.1-2.1 0-3.5 1.3-3.5 3.7V11H8v3h2.4v8h3.1Z"/></svg>';
			case 'instagram':
				return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M7.8 3h8.4A4.8 4.8 0 0 1 21 7.8v8.4a4.8 4.8 0 0 1-4.8 4.8H7.8A4.8 4.8 0 0 1 3 16.2V7.8A4.8 4.8 0 0 1 7.8 3Zm0 1.8A3 3 0 0 0 4.8 7.8v8.4a3 3 0 0 0 3 3h8.4a3 3 0 0 0 3-3V7.8a3 3 0 0 0-3-3H7.8Zm8.85 1.35a1.05 1.05 0 1 1 0 2.1 1.05 1.05 0 0 1 0-2.1ZM12 7.5A4.5 4.5 0 1 1 7.5 12 4.5 4.5 0 0 1 12 7.5Zm0 1.8A2.7 2.7 0 1 0 14.7 12 2.7 2.7 0 0 0 12 9.3Z"/></svg>';
			case 'linkedin':
				return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6.4 8.8H3.6V21h2.8V8.8ZM5 3A2 2 0 1 0 5 7a2 2 0 0 0 0-4Zm5.8 5.8H8.1V21h2.7v-6.4c0-1.7.3-3.3 2.4-3.3s2.1 2 2.1 3.4V21H18V14c0-3.4-.7-6-4.7-6-1.9 0-3.2 1-3.7 2.1h-.1V8.8Z"/></svg>';
			case 'twitter':
			case 'x':
				return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M18.9 3H22l-6.8 7.8L23 21h-6.1l-4.8-6.3L6.6 21H3.5l7.3-8.4L1 3h6.3l4.3 5.7L18.9 3Zm-1.1 16.1h1.7L6.4 4.8H4.6l13.2 14.3Z"/></svg>';
			case 'wordpress':
				return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm8.35 10a8.3 8.3 0 0 1-1.4 4.62l-2.99-8.19a2.93 2.93 0 0 0 1.7-.49A8.32 8.32 0 0 1 20.35 12ZM12 3.65a8.27 8.27 0 0 1 4.86 1.57h-.38c-.63 0-1.6.08-1.6.08a.26.26 0 0 0 .04.52s.52.04 1.07.08l1.59 4.35-2.38 7.12-3.95-11.47c.55-.04 1.04-.08 1.04-.08a.26.26 0 0 0 .04-.52s-1 .08-1.66.08c-.63 0-1.6-.08-1.6-.08a.26.26 0 0 0 .04.52s.52.04 1.07.08l2.36 6.47-.99 2.97-3.28-9.44c.55-.04 1.04-.08 1.04-.08a.26.26 0 0 0 .04-.52s-1 .08-1.66.08h-.32A8.29 8.29 0 0 1 12 3.65Zm-7.7 8.36a8.26 8.26 0 0 1 1.67-5l4.6 12.61A8.35 8.35 0 0 1 4.3 12Zm8.06 8.31a8.58 8.58 0 0 1-1.93-.22l2.05-5.95 2.1 5.75c.02.05.04.1.07.14a8.47 8.47 0 0 1-2.29.28Zm3.23-.65 2.09-6.04 1.07 2.93.02.08a8.35 8.35 0 0 1-3.18 3.03Z"/></svg>';
			case 'youtube':
				return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M21.6 7.2a3 3 0 0 0-2.1-2.1C17.7 4.5 12 4.5 12 4.5s-5.7 0-7.5.6A3 3 0 0 0 2.4 7.2 31.7 31.7 0 0 0 1.8 12c0 1.6.2 3.2.6 4.8a3 3 0 0 0 2.1 2.1c1.8.6 7.5.6 7.5.6s5.7 0 7.5-.6a3 3 0 0 0 2.1-2.1c.4-1.6.6-3.2.6-4.8s-.2-3.2-.6-4.8ZM9.8 15.3V8.7l5.7 3.3-5.7 3.3Z"/></svg>';
			default:
				return '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm6.9 9h-3.1a15.9 15.9 0 0 0-1.2-5 8.2 8.2 0 0 1 4.3 5Zm-6.9-6.2c.8 1.1 1.6 3.1 1.9 6.2h-3.8c.3-3.1 1.1-5.1 1.9-6.2ZM5.1 13h3.1a15.9 15.9 0 0 0 1.2 5 8.2 8.2 0 0 1-4.3-5Zm3.1-2H5.1a8.2 8.2 0 0 1 4.3-5 15.9 15.9 0 0 0-1.2 5Zm3.8 8.2c-.8-1.1-1.6-3.1-1.9-6.2h3.8c-.3 3.1-1.1 5.1-1.9 6.2Zm.4-8.2c-.3-2.5-.9-4.3-1.7-5.6.4-.1.8-.1 1.3-.1s.9 0 1.3.1c-.8 1.3-1.4 3.1-1.7 5.6Zm2.5 2h3.1a8.2 8.2 0 0 1-4.3 5 15.9 15.9 0 0 0 1.2-5Z"/></svg>';
		}
	}

	function getSocialNetworkKey( network ) {
		const key = String( network || '' )
			.trim()
			.toLowerCase()
			.replace( /[^a-z0-9_-]+/g, '-' )
			.replace( /^-+|-+$/g, '' );
		const aliases = {
			fb: 'facebook',
			'face-book': 'facebook',
			insta: 'instagram',
			'linkedin-in': 'linkedin',
			'linked-in': 'linkedin',
			linkedincom: 'linkedin',
			'twitter-x': 'x',
			'x-twitter': 'x',
			wp: 'wordpress',
			'wp-org': 'wordpress',
			'word-press': 'wordpress',
			'you-tube': 'youtube'
		};

		return aliases[ key ] || key;
	}

	function getSocialClassName( network ) {
		return 'is-' + getSocialNetworkKey( network );
	}

	function requestMembers( payload ) {
		const formData = new window.FormData();
		Object.keys( payload ).forEach( function( key ) {
			formData.append( key, payload[ key ] );
		} );

		return window.fetch( WsTeamShowcase.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: formData
		} ).then( function( response ) {
			return response.json();
		} );
	}

	function renderModalContent( member ) {
		const socials = ( member.socialLinks || [] ).map( function( socialLink ) {
			const socialUrl = safeUrl( socialLink.url );
			if ( ! socialUrl ) {
				return '';
			}

			return '<a class="blockforge-team-showcase__social-link ' + getSocialClassName( socialLink.network ) + '" href="' + socialUrl + '" target="_blank" rel="noopener noreferrer" aria-label="' + escapeHtml( socialLink.network ) + '">' + getSocialIcon( socialLink.network ) + '</a>';
		} ).join( '' );
		const profileImage = safeUrl( member.profileImage );

		return [
			'<div class="blockforge-team-showcase__modal-grid">',
			profileImage ? '<img src="' + profileImage + '" alt="' + escapeHtml( member.name ) + '" loading="lazy" />' : '<div class="blockforge-team-showcase__avatar-fallback" aria-hidden="true"><span>No Image</span></div>',
			'<div>',
			'<span class="blockforge-team-showcase__department">' + escapeHtml( member.department ) + '</span>',
			'<h3 class="blockforge-team-showcase__name">' + escapeHtml( member.name ) + '</h3>',
			'<p class="blockforge-team-showcase__designation"><strong>Designation:</strong> ' + escapeHtml( member.designation ) + '</p>',
			member.experience ? '<p class="blockforge-team-showcase__experience"><strong>Experience:</strong> ' + escapeHtml( member.experience ) + '</p>' : '',
			'<p class="blockforge-team-showcase__bio">' + escapeHtml( member.shortBio ) + '</p>',
			socials ? '<div class="blockforge-team-showcase__socials">' + socials + '</div>' : '',
			'</div>',
			'</div>'
		].join( '' );
	}

	function initializeBlock( block ) {
		const grid = block.querySelector( '[data-grid]' );
		const skeleton = block.querySelector( '.blockforge-team-showcase__skeleton' );
		const members = JSON.parse( block.dataset.members || '[]' );
		const perPage = parseInt( block.dataset.perPage || '9', 10 );
		const popupEnabled = block.dataset.popupEnabled === 'true';
		const filters = block.querySelectorAll( '.blockforge-team-showcase__filter' );
		const searchInput = block.querySelector( '.blockforge-team-showcase__search-input' );
		const loadMoreButton = block.querySelector( '.blockforge-team-showcase__load-more' );
		const loadMoreFooter = loadMoreButton ? loadMoreButton.closest( '.blockforge-team-showcase__footer' ) : null;
		const modal = block.querySelector( '.blockforge-team-showcase__modal' );
		const modalContent = modal ? modal.querySelector( '.blockforge-team-showcase__modal-content' ) : null;
		const darkToggle = block.querySelector( '.blockforge-team-showcase__dark-toggle' );
		const attributes = {
			gridColumns: parseInt( getComputedStyle( block ).getPropertyValue( '--bfts-columns' ), 10 ) || 3,
			cardAlignment: getComputedStyle( block ).getPropertyValue( '--bfts-text-align' ).trim() || 'left',
			cardHeight: getComputedStyle( block ).getPropertyValue( '--bfts-card-min-height' ).trim() || '100%',
			imagePosition: block.dataset.imagePosition || 'top',
			popupToggle: popupEnabled,
			cardBackground: getComputedStyle( block ).getPropertyValue( '--bfts-card-bg' ).trim() || '#ffffff',
			borderRadius: parseInt( getComputedStyle( block ).getPropertyValue( '--bfts-card-radius' ), 10 ) || 24,
			hoverEffect: 'lift',
			buttonBackground: getComputedStyle( block ).getPropertyValue( '--bfts-button-bg' ).trim() || '#111827',
			buttonTextColor: getComputedStyle( block ).getPropertyValue( '--bfts-button-color' ).trim() || '#ffffff',
			typographyFontSize: parseInt( getComputedStyle( block ).getPropertyValue( '--bfts-font-size' ), 10 ) || 16,
			typographyLineHeight: getComputedStyle( block ).getPropertyValue( '--bfts-line-height' ).trim() || '1.6'
		};
		let currentDepartment = '';
		let currentSearch = '';

		function renderNotice( message ) {
			return '<p class="blockforge-team-showcase__notice">' + escapeHtml( message ) + '</p>';
		}

		function setLoading( isLoading ) {
			if ( skeleton ) {
				skeleton.hidden = ! isLoading;
			}

			block.classList.toggle( 'is-loading', isLoading );
		}

		function updateLoadMoreVisibility( hasMore ) {
			if ( ! loadMoreButton ) {
				return;
			}

			loadMoreButton.hidden = ! hasMore;
			loadMoreButton.style.display = hasMore ? 'inline-flex' : 'none';

			if ( loadMoreFooter ) {
				loadMoreFooter.hidden = ! hasMore;
				loadMoreFooter.style.display = hasMore ? 'flex' : 'none';
			}
		}

		function bindCards() {
			grid.querySelectorAll( '.blockforge-team-showcase__card' ).forEach( function( card ) {
				if ( popupEnabled ) {
					const detailsTrigger = card.querySelector( '.blockforge-team-showcase__details-trigger' );

					if ( detailsTrigger ) {
						detailsTrigger.addEventListener( 'click', function( event ) {
							event.preventDefault();
							event.stopPropagation();

							const member = JSON.parse( card.dataset.member || '{}' );
							if ( modal && modalContent ) {
								modalContent.innerHTML = renderModalContent( member );
								modal.hidden = false;
								modal.setAttribute( 'aria-hidden', 'false' );
								document.body.classList.add( 'bfts-modal-open' );
							}
						} );
					}
				}

				card.addEventListener( 'mousemove', function( event ) {
					if ( ! card.classList.contains( 'has-hover-tilt' ) ) {
						return;
					}

					const rect = card.getBoundingClientRect();
					const x = ( event.clientX - rect.left ) / rect.width;
					const y = ( event.clientY - rect.top ) / rect.height;
					card.style.transform = 'rotateX(' + ( ( 0.5 - y ) * 8 ) + 'deg) rotateY(' + ( ( x - 0.5 ) * 10 ) + 'deg)';
				} );

				card.addEventListener( 'mouseleave', function() {
					card.style.transform = '';
				} );
			} );

			if ( window.gsap ) {
				window.gsap.fromTo(
					grid.querySelectorAll( '.blockforge-team-showcase__card' ),
					{ opacity: 0, y: 24 },
					{ opacity: 1, y: 0, duration: 0.45, stagger: 0.08, ease: 'power2.out' }
				);
			}
		}

		function runQuery( page, append ) {
			setLoading( true );

			requestMembers( {
				action: 'blockforge_team_showcase_query',
				nonce: WsTeamShowcase.nonce,
				members: JSON.stringify( members ),
				attributes: JSON.stringify( attributes ),
				page: page,
				perPage: perPage,
				department: currentDepartment,
				search: currentSearch
			} ).then( function( response ) {
				if ( ! response.success ) {
					return;
				}

				const markup = response.data && response.data.markup ? response.data.markup : '';
				const foundPosts = response.data && typeof response.data.foundPosts === 'number' ? response.data.foundPosts : 0;

				if ( append ) {
					grid.insertAdjacentHTML( 'beforeend', markup );
				} else {
					grid.innerHTML = foundPosts ? markup : renderNotice( WsTeamShowcase.i18n.noResults || 'No teammates found' );
				}

				if ( loadMoreButton ) {
					updateLoadMoreVisibility( !! response.data.hasMore );
					loadMoreButton.dataset.nextPage = response.data.nextPage;
					loadMoreButton.dataset.currentDepartment = currentDepartment;
					loadMoreButton.dataset.currentSearch = currentSearch;
				}

				bindCards();
			} ).catch( function() {
				grid.innerHTML = '<p class="blockforge-team-showcase__notice">Unable to load team members.</p>';
			} ).finally( function() {
				setLoading( false );
			} );
		}

		filters.forEach( function( filterButton ) {
			filterButton.addEventListener( 'click', function() {
				filters.forEach( function( button ) {
					button.classList.remove( 'is-active' );
				} );
				filterButton.classList.add( 'is-active' );
				currentDepartment = filterButton.dataset.department || '';
				runQuery( 1, false );
			} );
		} );

		if ( searchInput ) {
			let searchTimeout = null;

			searchInput.addEventListener( 'input', function() {
				window.clearTimeout( searchTimeout );
				currentSearch = searchInput.value.trim();
				searchTimeout = window.setTimeout( function() {
					runQuery( 1, false );
				}, 250 );
			} );
		}

		if ( loadMoreButton ) {
			updateLoadMoreVisibility( members.length > perPage );

			loadMoreButton.addEventListener( 'click', function() {
				runQuery( parseInt( loadMoreButton.dataset.nextPage || '2', 10 ), true );
			} );
		}

		if ( modal ) {
			modal.addEventListener( 'click', function( event ) {
				if ( event.target.hasAttribute( 'data-close-modal' ) ) {
					modal.hidden = true;
					modal.setAttribute( 'aria-hidden', 'true' );
					document.body.classList.remove( 'bfts-modal-open' );
				}
			} );
		}

		if ( darkToggle ) {
			const toggleLabel = darkToggle.dataset.label || 'Dark Mode';
			const stateOn = darkToggle.dataset.stateOn || 'On';
			const stateOff = darkToggle.dataset.stateOff || 'Off';
			const labelNode = darkToggle.querySelector( '.blockforge-team-showcase__dark-toggle-label' );
			const stateNode = darkToggle.querySelector( '.blockforge-team-showcase__dark-toggle-state' );

			darkToggle.addEventListener( 'click', function() {
				const pressed = darkToggle.getAttribute( 'aria-pressed' ) === 'true';
				const nextPressed = ! pressed;

				darkToggle.setAttribute( 'aria-pressed', String( nextPressed ) );
				if ( labelNode ) {
					labelNode.textContent = toggleLabel;
				}
				if ( stateNode ) {
					stateNode.textContent = nextPressed ? stateOn : stateOff;
				}
				block.classList.toggle( 'is-dark-mode', nextPressed );
			} );
		}

		bindCards();
	}

	document.addEventListener( 'DOMContentLoaded', function() {
		document.querySelectorAll( '.blockforge-team-showcase' ).forEach( initializeBlock );
	} );
} )();
