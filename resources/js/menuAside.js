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
  mdiFileDocumentEditOutline,mdiHumanMaleBoard,mdiAccountSchool

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
    icon: mdiFileDocumentEditOutline ,
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
    href:"/persona",
    label: "Registrar Personas",
    icon: mdiBookshelf,
    role: "Admin",
   
  }, 
  {
    href:"/persona",
    label: "Registrar Personas",
    icon: mdiBookshelf,
    role: "Alumno",
   
  }, 
  {
    href:"/persona",
    label: "Registrar Personas",
    icon: mdiBookshelf,
    role: "Tutor",
   
  },  



  
 

  {
    label: "Catálogos",
    icon: mdiFileDocumentEditOutline ,
    role: "Admin",
    menu: [
      {
        href:"/empresa",
        label: "Empresa",
        icon: mdiBookshelf,
        role: "Admin",
      }, 
      {
        href:"/empresa",
        label: "Documento",
        icon: mdiBookshelf,
        role: "Admin",
      }, 
      {
        href:"/empresa",
        label: "Departamento",
        icon: mdiBookshelf,
        role: "Admin",
      }, 
    ]
  },


  {
    href:"/tecnico",
    label: "Documentos Técnicos ",
    icon: mdiBookshelf,
    role: "Admin",
  }, 
  {
    href:"/tecnico",
    label: "Documentos Legales ",
    icon: mdiBookshelf,
    role: "Admin",
  }, 


  
];
