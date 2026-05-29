import React from 'react';
import { Head } from '@inertiajs/react';
import PlatformApp from '../../platform-ui/platform-app';
import '../../../css/journal-home.css';
import '../../../css/platform-home.css';
import '../../journal-ui/styles.css';

export default function PlatformHome(props) {
    return (
        <>
            <Head>
                <title>{props.pageTitle}</title>
                <meta
                    name="description"
                    content="Open research in mind, brain and behaviour. Diamond open-access journals from Nexara Research Press."
                />
            </Head>
            <PlatformApp {...props} />
        </>
    );
}
