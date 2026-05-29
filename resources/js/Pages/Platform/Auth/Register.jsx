import React from 'react';
import { Head } from '@inertiajs/react';
import AuthPage from '../../../platform-ui/auth-page';
import '../../../../css/journal-home.css';
import '../../../../css/auth.css';
import '../../../journal-ui/styles.css';

export default function PlatformRegister(props) {
    return (
        <>
            <Head>
                <title>{props.pageTitle}</title>
                <meta name="description" content="Create your free Nexara account." />
            </Head>
            <AuthPage {...props} />
        </>
    );
}
