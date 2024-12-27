const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
import Select from './select';
import EditCollection from './edit';
import { Icon } from '@wpbr/components';

export default registerBlockType( 'wpbr/collection', {
    title: __( 'Review Collection', 'wp-business-reviews' ),
    description: __( 'The Reviews Collection block inserts an existing review collection onto the page. The collection presentation can be adjusted in the collection builder.', 'wp-business-reviews' ),
    category: 'wpbr',
    icon: Icon,
    keywords: [
        __( 'collection', 'wp-business-reviews' ),
        __( 'reviews', 'wp-business-reviews' ),
        __( 'business', 'wp-business-reviews' ),
        __( 'wpbr', 'wp-business-reviews' ),
    ],
    supports: {
        html: false,
    },
    attributes: {
        id: {
            type: 'number',
            default: 0,
        },
    },
    edit: ( props ) => {
        const { attributes, setAttributes } = props;

        if ( ! attributes.id ) {
            return <Select onChange={ id => setAttributes( { id } ) }/>;
        }

        return <EditCollection { ...props } />;
    },
    save: () => null,
} );

