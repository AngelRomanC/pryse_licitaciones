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
    href: "/dashboard",
    to: "/dashboard",
    icon: mdiMonitor,
    label: "Dashboard",
    role: "Alumno" // Vistas 
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
        href:"/alumno",
        label: "Alumno",
        icon: mdiAccountSchool  ,
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
    label: "Catálogos",
    icon: mdiLibrary ,
    role: "Alumno",
    menu: [
      {
        href:"/empresa",
        label: "Empresa",
        icon: mdiBookshelf,
        role: "Alumno",
      }, 
      {
        href:"/tipo-de-documento",
        label: "Documento",
        icon: mdiFileDocumentOutline,
        role: "Alumno",
      }, 
      {
        href:"/departamento",
        label: "Departamento",
        icon: mdiAccountGroup,
        role: "Alumno",
      }, 
      {
        href:"/modalidad",
        label: "Modalidades",
        icon: mdiFormatListChecks,
        role: "Alumno",
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
    href:"/respaldo",
    label: "Respaldo DB",
    icon: mdiBookshelf,
    role: "Admin",
  },

  //ALUMNOOOOOOO
  {
    href:"/documento",
    label: "Documentos Técnicos ",
    icon: mdiFileSign,
    role: "Alumno",
  }, 
  {
    href:"/documento-legal",
    label: "Documentos Legales ",
    icon: mdiScaleBalance,
    role: "Alumno",
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
];
