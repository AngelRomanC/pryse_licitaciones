import {
  mdiAccountCircle,
  mdiMonitor,
  mdiGithub,
  mdiLock,
  mdiAlertCircle,
  mdiSquareEditOutline,
  mdiTable,
  mdiViewListOutline,
  mdiTelevisionGuide,
  mdiResponsive,
  mdiSecurity,
  mdiClockCheck,
  mdiBookshelf,
  mdiCalendarAccountOutline,
  mdiClockTimeEleven,
  mdiAccount,
  mdiBedClock,
  mdiEmoticonHappyOutline ,
  mdiFileDocumentOutline,
  mdiFileDocumentEditOutline,mdiHumanMaleBoard,mdiAccountSchool,
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
    role: "Admin"
  },

  
  {
    label: "Catalogo",
    icon: mdiViewListOutline,
    role: "Alumno",
    menu: [
 
      {
        label: "Materias",
        href: "/materia",
        to: "/materia",
        
        icon: mdiBookshelf,
      },
      {
        label: "Periodo",
        href: "/periodo",
        icon: mdiBookshelf,
       
      },
    ],
  },


  {
    href:"/grupo",
    label: "Grupos",
    icon: mdiBookshelf,
    role: "Alumno",
  }, 
  {
    label: "Usuarios",
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
        href:"/profesor",
        label: "Profesor",
        icon: mdiHumanMaleBoard,
        role: "Admin",
      }, 
      {
        href:"/alumno",
        label: "Alumno",
        icon: mdiAccountSchool  ,
        role: "Admin",
      }, 
      
    ]
  },

  
  {
    href:"/recursamiento",
    label: "Recursamiento",
    icon: mdiBookshelf,
    role: "Alumno",
  }, 


  {
    href:"/lista",
    label: "Recursamientos disponibles",
    icon: mdiBookshelf,
    role: "Alumno",
  }, 
  {
    label: "Formatos",
    icon: mdiFileDocumentEditOutline ,
    role: "Alumno",
    menu: [
      {
        href:"/academico",
        label: "Academico",
        icon: mdiBookshelf,
        role: "Alumno",
      }, 
      {
        href:"/habito",
        label: "Habitos",
        icon: mdiBedClock,
        role: "Alumno",
      }, 
      {
        href:"/inteligencia",
        label: "Inteligencia",
        icon: mdiEmoticonHappyOutline ,
        role: "Alumno",
      }, 
      {
        href:"/encuesta",
        label: "Encuesta recursamientos",
        icon: mdiBookshelf,
        role: "Alumno",
      }, 
    ]
  },
  
  {
    href:"/evaluacion",
    label: "Evaluación",
    icon: mdiBookshelf,
    role: "Tutor",
  }, 
  {
    href:"/pregunta",
    label: "Formatos",
    icon: mdiBookshelf,
    role: "Alumno",
  }, 
  {
    href:"/respaldo",
    label: "Respaldo DB",
    icon: mdiBookshelf,
    role: "Admin-",
  },
  {
    href:"/observaciones",
    label: "Observaciones",
    icon: mdiBookshelf,
    role: "Alumno",
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


  
];
