const { __ } = wp.i18n;
const { InspectorControls } = wp.blockEditor;
const { Button, PanelBody } = wp.components;

import { useFetcher } from '@wpbr/js/api';
import { Error, Loader, Review, ReviewContainer } from '@wpbr/components';

export default ( { attributes, setAttributes } ) => {
    const { data, isLoading, isError } = useFetcher( '/get-collection', { id: attributes.id } );

    if ( isLoading ) {
        return <Loader />;
    }

    if ( isError ) {
        return <Error text={ __( 'There was an error while fetching data for this block', 'wp-business-reviews' ) } />
    }

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Collection Settings', 'wp-business-reviews' ) }>
                    <Button isSecondary onClick={ () => setAttributes( { id: 0 } ) }>
                        { __( 'Change Collection', 'wp-business-reviews' ) }
                    </Button>
                </PanelBody>
            </InspectorControls>

            <ReviewContainer
                format={ data.settings.format }
                style={ data.settings.style }
                maxColumns={ data.settings.max_columns }
            >
                { data.reviews.map( review => (
                    <Review
                        key={ review.post_id }
                        postId={ review.post_id }
                        platform={ review.platform }
                        components={ review.components }
                        settings={ data.settings }
                    />
                ) ) }
            </ReviewContainer>
        </>
    );
};
