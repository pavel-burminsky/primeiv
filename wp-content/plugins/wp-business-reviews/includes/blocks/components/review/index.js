const { __ } = wp.i18n;
import { mapPlatformIdToName } from '@wpbr/js/platform-mapper';
import { renderRating } from '@wpbr/js/rating';
import truncate from 'lodash.truncate';

export default ( {
    platform,
    components,
    postId,
    settings,
} ) => {

    const renderReviewerName = name => {
        if ( ! name && 'placeholder' !== platform ) {
            return null;
        }

        return (
            <h3 className="wpbr-review__reviewer-name">{ name }</h3>
        );
    };

    const renderReviewerImage = () => {
        let className = 'wpbr-review__reviewer-image';
        let reviewerImage = components.reviewer_image_custom ?? components.reviewer_image;

        if ( ! reviewerImage ) {
            return (
                <div className={ `${ className } wpbr-review__reviewer-image--placeholder` }></div>
            );
        }

        if ( components.reviewer_image_custom ) {
            className += ' wpbr-review__reviewer-image--custom';
        }

        return (
            <div className={ className }>
                <img src={ reviewerImage }/>
            </div>
        );
    };

    const getClassNames = () => {
        const platformSlug = platform.replace( '_', '-' );

        return `wpbr-review wpbr-review-${ postId ?? 0 } wpbr-theme-${ platformSlug } js-wpbr-review`;
    };

    const renderTimestamp = () => {
        const platformName = mapPlatformIdToName( platform );

        if ( ! components.formatted_date ) {
            return null;
        }

        if ( 'placeholder' === platform ) {
            return (
                <div className="wpbr-review__timestamp"></div>
            );
        }

        return (
            <span className="wpbr-review__timestamp">
				{ components.formatted_date }
                { platformName && ` ${ __( 'via', 'wp-business-reviews' ) } ${ platformName }` }
			</span>
        );
    };

    const renderRecommendation = recommendation => {
        if ( ! [ 'positive', 'negative' ].includes( recommendation ) ) {
            return null;
        }

        const recommendationString = ( 'negative' === recommendation )
            ? __( 'doesn\'t recommend', 'wp-business-reviews' )
            : __( 'recommends', 'wp-business-reviews' );

        return (
            <div className="wpbr-review__reco">
                <div className="wpbr-reco">
                    <i className={ `wpbr-reco__icon wpbr-reco__icon--${ recommendation }` }></i>
                    <span className="wpbr-reco__text">
                        { recommendationString }
                        { components.review_source_name && (
                            <a
                                href={ components.review_source_url }
                                target="_blank"
                                title={ components.review_source_name }
                                rel="noopener noreferrer"
                            >
                                { components.review_source_name }
                            </a>
                        ) }
                    </span>
                </div>
            </div>
        );
    };

    const renderContent = ( maxCharacters = 0, lineBreaks = 'disabled' ) => {
        if ( 'placeholder' === platform ) {
            return (
                <div className="wpbr-review__content"></div>
            );
        }

        let content = components.content;
        let isTruncated = [ 'yelp', 'zomato' ].includes( platform );

        if ( ! content ) {
            return null;
        }

        if ( 0 < maxCharacters ) {
            content = truncate(
                content,
                {
                    'length': maxCharacters,
                    'omission': '...',
                    'separator': /[.?!,]? +/,
                },
            );

            if ( content !== components.content ) {
                isTruncated = true;
            }
        }

        if ( 'enabled' === lineBreaks ) {
            let arrayOfStrings = content.split( '\n' );

            if ( isTruncated && components.review_url ) {
                arrayOfStrings[ arrayOfStrings.length - 1 ] += renderOmission();
            }

            content = arrayOfStrings.map( string => <p>{ string }</p> );
        } else if ( isTruncated && components.review_url ) {
            content = <p>{ content } { renderOmission() }</p>;
        } else {
            content = <p>{ content }</p>;
        }

        return (
            <div className="wpbr-review__content">
                { content }
            </div>
        );
    };

    const renderOmission = () => (
        <a
            className="wpbr-review__omission"
            href={ components.review_url }
            target="_blank"
            rel="noopener noreferrer"
        >
            { __( 'Read more', 'wp-business-reviews' ) }
        </a>
    );

    const isEnabled = prop => 'enabled' === prop;

    return (
        <div className="wpbr-collection__item js-wpbr-collection-item">
            <div className={ getClassNames() }>
                <div className="wpbr-review__header">
                    { isEnabled( settings.review_components.reviewer_image ) && renderReviewerImage() }
                    <div className="wpbr-review__details">
                        { isEnabled( settings.review_components.platform_icon ) && (
                            <i className="wpbr-review__platform-icon"></i>
                        ) }
                        { isEnabled( settings.review_components.reviewer_name ) && renderReviewerName( components.reviewer_name ) }
                        { isEnabled( settings.review_components.rating ) && ! isNaN( components.rating ) && (
                            <span className="wpbr-review__rating" dangerouslySetInnerHTML={ {
                                __html: renderRating( components.rating, components.platform ),
                            } }></span>
                        ) }
                        { isEnabled( settings.review_components.recommendation ) && renderRecommendation( components.rating ) }
                        { isEnabled( settings.review_components.timestamp ) && renderTimestamp() }
                    </div>
                </div>
                { isEnabled( settings.review_components.content ) && renderContent( settings.max_characters, settings.line_breaks ) }
            </div>
        </div>
    );
}
