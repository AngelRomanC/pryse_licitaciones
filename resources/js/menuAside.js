import {
  mdiMonitor,
  mdiSecurity,
  mdiBookshelf,
  mdiFileDocumentOutline,
  mdiAccountTie ,mdiAccountSchool,
  mdiAccountGroup,
  mdiScaleBalance,
  mdiFileSign,
  mdiLibrary,
  mdiFormatListChecks, 
  mdiGavel , 
} from "@mdi/js";

export default [
  {
    href: "/dashboard",
    to: "/dashboard",
    icon: mdiMonitor,
    label: "Dashboard",
    role: "Admin" //Vistas 
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
    label: "Catálogos",
    icon: mdiLibrary ,
    role: "Admin",
    menu: [
      {
        href:"/empresa",
        label: "Empresa",
        icon: mdiBookshelf,
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
        icon: mdiAccountGroup,
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
 

  //Modulos a Usuario de sistema 
  {
    href: "/dashboard",
    to: "/dashboard",
    icon: mdiMonitor,
    label: "Dashboard",
    role: "Usuario" // Vistas 
  },

  {
    label: "Catálogos",
    icon: mdiLibrary ,
    role: "Usuario",
    menu: [
      {
        href:"/empresa",
        label: "Empresa",
        icon: mdiBookshelf,
        role: "Usuario",
      }, 
      {
        href:"/tipo-de-documento",
        label: "Documento",
        icon: mdiFileDocumentOutline,
        role: "Usuario",
      }, 
      {
        href:"/departamento",
        label: "Departamento",
        icon: mdiAccountGroup,
        role: "Usuario",
      }, 
      {
        href:"/modalidad",
        label: "Modalidades",
        icon: mdiFormatListChecks,
        role: "Usuario",
      }, 
    ]
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
  {
    href:"/licitacion",
    label: "Licitaciones",
    icon: mdiGavel ,
    role: "Usuario",
  }, 
];
