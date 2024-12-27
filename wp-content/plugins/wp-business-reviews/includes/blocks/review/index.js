const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
import Select from './select';
import EditReview from './edit';
import { Icon } from '@wpbr/components';

export default registerBlockType( 'wpbr/review', {
    title: __( 'Review', 'wp-business-reviews' ),
    description: __( 'Display review', 'wp-business-reviews' ),
    category: 'wpbr',
    icon: Icon,
    keywords: [
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

        return <EditReview { ...props } />;
    },
    save: () => null,
} );
