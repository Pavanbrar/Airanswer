import React from 'react'
import HomeIcon from '@material-ui/icons/Home';
import MailIcon from '@material-ui/icons/Mail';
import DashboardIcon from '@material-ui/icons/Dashboard';
import DevicesIcon from '@material-ui/icons/Devices';
export const Sidebardata = [
    {
        title:'Home',
        icon: <HomeIcon />,
        link : 'dashboard'
    },
    {
        title:'Device',
      //  icon: '=',
        icon: <DevicesIcon />,
        link : 'device'
    }, {
        title:'Faq',
      //  icon: '=',
        icon: <DashboardIcon />,
        link : 'faq'
    },
]

//export default Sidebardata
