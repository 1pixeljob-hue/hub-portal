export const categories = [
    { id: 'work', name: 'Work', icon: 'work', count: 12, colorClass: 'text-indigo-600 dark:text-indigo-400', bgClass: 'bg-indigo-50 dark:bg-indigo-900/30' },
    { id: 'personal', name: 'Personal', icon: 'person', count: 8, colorClass: 'text-purple-600 dark:text-purple-400', bgClass: 'bg-purple-50 dark:bg-purple-900/30' },
    { id: 'social', name: 'Social', icon: 'groups', count: 4, colorClass: 'text-pink-600 dark:text-pink-400', bgClass: 'bg-pink-50 dark:bg-pink-900/30' },
    { id: 'research', name: 'Research', icon: 'school', count: 25, colorClass: 'text-cyan-600 dark:text-cyan-400', bgClass: 'bg-cyan-50 dark:bg-cyan-900/30' }
];

export const mockLinks = [
    {
        id: '1',
        title: 'Design System V2',
        url: 'figma.com/file/Ck234.../Design-System',
        logoUrl: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDuBC11lWoxftwwrL6dMgzlXJ08aZ3DQ1TaqlL7s5mox4k1xEBVWFHbAYUlz430BcfsNtQIE77ARAJ_pX8cwCMAE6uSPJ-vcRXh_YOkGsmgxPv6YCraJNawBsOeUJJDrjzxc4aowSl8P9hu_iTy21uHuDY2Kw3qWPUsq824ER7zDx_ShThU9ViKFIPoYlkLQJS_9996Uh3ZDx9splZgU-n-EM4W_95ef_UDIqixiDCyIQeS_sOpDB9CSTFeMNmnWDbjbg9BvesabXK1',
        theme: 'blue',
        tags: [
            { name: 'Work', type: 'primary', color: 'indigo' },
            { name: 'UI/UX', type: 'secondary', color: 'slate' }
        ]
    },
    {
        id: '2',
        title: 'Tailwind CSS Docs',
        url: 'tailwindcss.com/docs/installation',
        logoUrl: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDPnsRuf2uGfe_-uZFZRzMG4kJpu5wuRUMQQiZNC9JlFMK0z7NOhDdjAzgcPXmb_aLaxfC-qDGGkpy1ABeKAEKsojym2vp3CF3S2QHbyXhJmwGtNDNMJNcWR-6yJAprlunF8FmriCPWHy79EbQa4B8VXa454q6khlnaHhVa6VRBy6E0sZrioYQT6jCHp9ltLpb16jJTyM9F4mEdt69qakjYKxoZhAb9Bg5fMYjQuMmBfzKGiwO8oJdxlQIEhYIvGUIz-CprLqeivx53',
        theme: 'emerald',
        tags: [
            { name: 'Research', type: 'primary', color: 'emerald' },
            { name: 'Dev', type: 'secondary', color: 'slate' }
        ]
    },
    {
        id: '3',
        title: 'Linear - Roadmap',
        url: 'linear.app/team-alpha/roadmap',
        initial: 'L',
        theme: 'purple',
        tags: [
            { name: 'Work', type: 'primary', color: 'indigo' },
            { name: 'Planning', type: 'secondary', color: 'slate' }
        ]
    },
    {
        id: '4',
        title: 'Q3 Financial Reports',
        url: 'drive.google.com/drive/folders/1a...',
        logoUrl: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAMQjkKbdVQfFspIaIZIv2sjGLJeFE2jIngh8tlQqyOqeA1jmExDjxmmFjxtARVcM9eXTAh7mLGOUCFWAo3Fp2V8LZykAXIaXg-bjj_NcdU1YBBo7_L1WsEgooDBBbZkAtDPVwiCSAHY8WIJ_-Nku_2qIbmabwYDpcfr7HHTQHFtRRteXZD-Ye5DZPWp3TB08oEFVKwaa8pGzLrm5feKJqUMBSnB3BFa9WZ6jtOI9BKlvnVBiU6yQKhmRGAvw6GOCe_hi-CeMowvUof',
        theme: 'yellow',
        tags: [
            { name: 'Personal', type: 'primary', color: 'yellow' },
            { name: 'Finance', type: 'secondary', color: 'slate' }
        ]
    },
    {
        id: '5',
        title: 'r/webdesign Inspiration',
        url: 'reddit.com/r/web_design',
        logoUrl: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCKOr-N1_5tcv0ysMeHW_eQ4-mydbCtSaXMxoaE4K3eyLe0j7TrpHqL5l_mD7gbLxj8_BRcfm4DnxgmZFr_pjWUp8jdhQaAR-8VpfGe0I3oymR6avkeaiWB88_7OgJCrFMZTmtJOR1gitBKqVkc1gRwmAXV0sH9ZdRYCN0_NTAB9AYYd5IrhwpCy2etfMwIJoEOvWYPPtCMHjhNrCVjMR5EYTxu81PsqxVzP4LHd2WpH5LaIn1vNX7UMs3-rp2WrFwUFmXIIAApH_P4',
        theme: 'orange',
        tags: [
            { name: 'Social', type: 'primary', color: 'pink' },
            { name: 'Inspiration', type: 'secondary', color: 'slate' }
        ]
    },
    {
        id: '6',
        title: 'UX Case Study - Airbnb',
        url: 'medium.com/@author/airbnb-ux...',
        initialIcon: 'article',
        theme: 'gray',
        tags: [
            { name: 'Research', type: 'primary', color: 'cyan' },
            { name: 'Reading', type: 'secondary', color: 'slate' }
        ]
    }
];
