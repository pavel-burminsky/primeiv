const { __ } = wp.i18n;
const { useSelect } = wp.data;
import { Container, Select, Loader, Icon } from '@wpbr/components';

export default ( { onChange } ) => {
    const { reviews } = useSelect( select => ( {
        reviews: select( 'core' ).getEntityRecords( 'postType', 'wpbr_review', { per_page: -1 } ),
    } ) );

    const handleChange = e => onChange( Number( e.currentTarget.value ) );

    if ( ! reviews ) {
        return <Loader/>;
    }

    if ( reviews && reviews.length === 0 ) {
        return (
            <Container textAlign="center" className="wpbr-block-blank-slate">
                <Icon size={ 80 }/>
                <h3 className="wpbr-block-blank-slate__heading">
                    { __( 'WP Business Reviews - Collection Block', 'wp-business-reviews' ) }
                </h3>
                <h4>
                    { __( 'There are no reviews at this time.', 'wp-business-reviews' ) }
                </h4>
                <a className="button" href={ window.wpbrData.reviewsAdminUrl }>
                    { __( 'Add Review', 'wp-business-reviews' ) }
                </a>
            </Container>
        );
    }

    const options = reviews.map( review => ( {
        value: review.id,
        label: review.title.rendered,
    } ) );

    return (
        <Container textAlign="center" className="wpbr-block-blank-slate">
            <div className="block-loaded">
                <Icon size={ 80 }/>
                <h3 className="wpbr-block-blank-slate__heading">
                    { __( 'WP Business Reviews - Single Review Block', 'wp-business-reviews' ) }
                </h3>
                <Select
                    options={ options }
                    defaultText={ __( 'Select Review', 'wp-business-reviews' ) }
                    onChange={ handleChange }
                />
            </div>
        </Container>
    );
};
