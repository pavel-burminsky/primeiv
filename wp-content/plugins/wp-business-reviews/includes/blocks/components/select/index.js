const { __ } = wp.i18n;
const { withInstanceId } = wp.compose;

const Select = ( { label, options, onChange, defaultText, instanceId } ) => {
    const id = `wpbr-select-collection${ instanceId }`;
    const styles = {
        label: {
            display: 'block',
            fontWeight: 'bold',
        },
        select: {
            width: '100%',
            border: 1,
            borderStyle: 'solid',
            borderRadius: 5,
            marginTop: 10,
            fontSize: 14,
        },
    };

    return (
        <div>
            { label && (
                <label style={ styles.label } htmlFor={ id }>
                    { label }
                </label>
            ) }
            <select style={ styles.select } id={ id } onChange={ onChange }>
                <option defaultChecked>-- { defaultText ?? __( 'Select', 'wp-business-reviews' ) } --</option>
                { options.map( option => {
                    return (
                        <option
                            key={ option.value }
                            value={ option.value }
                        >
                            { option.label }
                        </option>
                    );
                } ) }
            </select>
        </div>
    );
};

export default withInstanceId( Select );
