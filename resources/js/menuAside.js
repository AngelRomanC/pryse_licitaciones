import {
  mdiMonitor,
  mdiSecurity,
  mdiBriefcase,
  mdiFileDocumentOutline,
  mdiAccountTie ,
  mdiAccountGroup,
  mdiScaleBalance,
  mdiFileSign,
  mdiLibrary,
  mdiFormatListChecks, 
  mdiGavel , 
  mdiViewDashboard,
  mdiClipboardList,
  mdiShieldAccount ,
  mdiOfficeBuilding,
  mdiAccountMultiple,
  mdiViewModule,
  mdiKeyChain,
  mdiShieldKey 

} from "@mdi/js";

export default [
  {
    href: "/dashboard",
    to: "/dashboard",
    icon: mdiMonitor,
    label: "Dashboard",
    role: "Admin" 
  },

  {
    label: "Gestión de Usuarios",
    icon: mdiAccountGroup ,
    role: "Admin",
    menu: [
      {
        href:"/usuarios",
        label: "Admin",
        icon: mdiSecurity,
        role: "Admin",
      }, 
      {
        href:"/usuarios-sistema",
        label: "Usuario",
        icon: mdiAccountTie   ,
        role: "Admin",
      }, 
      
    ]
  },
  {
  label: "Seguridad",
  icon: mdiShieldKey,
  role: "Admin",
  menu: [
    {
        href:"/users",
        label: "Usuarios",
        icon: mdiAccountMultiple,
        role: "Admin",
    }, 
    {
      route: "roles.index",
      label: "Roles",
      icon: mdiShieldAccount,
      role: "Admin",
    },
    
    {
      route: "modules.index",
      label: "Módulos",
      icon: mdiViewModule,
      role: "Admin",
    },
    {
      route: "permissions.index",
      label: "Permisos",
      icon: mdiKeyChain,
      role: "Admin",
    }, 
  ],
},
     
  {
    label: "Catálogos",
    icon: mdiLibrary ,
    role: "Admin",
    menu: [
      {
        href:"/empresa",
        label: "Empresa",
        icon: mdiBriefcase,
        role: "Admin",
      }, 
      {
        href:"/tipo-de-documento",
        label: "Documento",
        icon: mdiFileDocumentOutline,
        role: "Admin",
      }, 
      {
        href:"/departamento",
        label: "Departamento",
        icon: mdiOfficeBuilding,
        role: "Admin",
      }, 
      {
        href:"/modalidad",
        label: "Modalidades",
        icon: mdiFormatListChecks,
        role: "Admin",
      }, 
    ]
  },
  
  {
    href:"/documento",
    label: "Documentos Técnicos ",
    icon: mdiFileSign,
    role: "Admin",
  }, 
  {
    href:"/documento-legal",
    label: "Documentos Legales ",
    icon: mdiScaleBalance,
    role: "Admin",
  }, 
    {
    href:"/licitacion",
    label: "Licitaciones",
    icon: mdiGavel ,
    role: "Admin",
  },  

  // Modulos de Usuario.
  {
  label: "Dashboard",
  icon: mdiViewDashboard,
  role: "Usuario",
  menu: [
    {
        href:"/dashboard",
        label: "Documentos Vigentes",
        icon: mdiClipboardList,
        role: "Usuario",
    }, 
    {
      route: "dashboard.vencidos",
      label: "Documentos Vencidos",
      icon: mdiShieldAccount,
      role: "Usuario",
    },
    
   
  ],
},


  {
    href:"/documento",
    label: "Documentos Técnicos ",
    icon: mdiFileSign,
    role: "Usuario",
  }, 
  {
    href:"/documento-legal",
    label: "Documentos Legales ",
    icon: mdiScaleBalance,
    role: "Usuario",
  }, 
];
