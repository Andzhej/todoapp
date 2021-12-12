import React, {Fragment, useContext} from 'react';
import {Snackbar} from "@mui/material";
import {TodoContext} from "../contexts/TodoContext";
import MuiAlert from '@mui/material/Alert';

function AppSnackbar() {
    const context = useContext(TodoContext);

    const Alert = React.forwardRef(function Alert(props, ref) {
        return <MuiAlert elevation={6} ref={ref} variant="filled" {...props} />;
    });

    const handleClose = () => {
        context.setMessage(false);
    };

    return (
        <Snackbar
            anchorOrigin={{vertical: "top", horizontal: "right"}}
            autoHideDuration={6000}
            open={context.message.text !== undefined}
            onClose={handleClose}
        >
            <Alert severity={context.message.level}>
                {context.message.text}
            </Alert>
        </Snackbar>
    );
}

export default AppSnackbar;