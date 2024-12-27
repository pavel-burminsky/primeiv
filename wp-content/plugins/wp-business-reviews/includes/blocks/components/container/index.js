export default ( { children, textAlign = 'inherit', ...rest } ) => {
    return (
        <div {...rest}>
            {children}
        </div>
    );
};
