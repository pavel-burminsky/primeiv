export default ( { children, format, maxColumns, style } ) => {
    const getClassNames = () => {
        let baseName = 'wpbr-collection';
        let className = '';
        let classModifiers = [];

        switch ( format ) {
            case 'review_gallery':
                if ( 0 < maxColumns ) {
                    classModifiers = [ 'gallery', `${ maxColumns }-col` ];
                } else {
                    classModifiers = [ 'gallery', 'auto-fit' ];
                }
                break;
            case 'review_list':
                classModifiers = [ 'list' ];
                break;
            case 'review_carousel':
                classModifiers = [ 'carousel' ];
                break;
        }

        className = baseName;

        for ( const modifier of classModifiers ) {
            className += ` wpbr-collection--${ modifier }`;
        }

        return className.trim();
    };

    return (
        <div className="wpbr-wrap">
            <div className="wpbr-collection-wrap js-wpbr-collection-wrap">
                <div className={ `wpbr-theme-${ style } ${ getClassNames() } js-wpbr-collection` }>
                    { children }
                </div>
            </div>
        </div>
    );
}
