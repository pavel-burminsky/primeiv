const { __ } = wp.i18n;
const { useSelect } = wp.data;
import { Container, Select, Loader, Icon } from '@wpbr/components';

export default ( { onChange } ) => {
    const { collections } = useSelect( select => ( {
        collections: select( 'core' ).getEntityRecords( 'postType', 'wpbr_collection', { per_page: -1 } ),
    } ) );

    const handleChange = e => onChange( Number( e.currentTarget.value ) );

    if ( ! collections ) {
        return <Loader/>;
    }

    if ( collections && collections.length === 0 ) {
        return (
            <Container textAlign="center" className="wpbr-block-blank-slate">
                <Icon size={ 80 }/>
                <h3 className="wpbr-block-blank-slate__heading">
                    { __( 'WP Business Reviews Collection', 'wp-business-reviews' ) }
                </h3>
                <h4>
                    { __( 'There are no collections at this time.', 'wp-business-reviews' ) }
                </h4>
                <a className="button" href={ window.wpbrData.collectionsAdminUrl }>
                    { __( 'Add Collection', 'wp-business-reviews' ) }
                </a>
            </Container>
        );
    }

    const options = collections.map( collection => ( {
        value: collection.id,
        label: collection.title.rendered,
    } ) );

    return (
        <Container textAlign="center" className="wpbr-block-blank-slate">
            <div className="block-loaded">
                <Icon size={ 80 }/>
                <h3 className="wpbr-block-blank-slate__heading">
                    { __( 'WP Business Reviews Collection', 'wp-business-reviews' ) }
                </h3>
                <Select
                    options={ options }
                    defaultText={ __( 'Select Collection', 'wp-business-reviews' ) }
                    onChange={ handleChange }
                />
            </div>
        </Container>
    );
};
