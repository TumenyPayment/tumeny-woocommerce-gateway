(()=>{
	"use strict";
	const e=window.React,
		t=window.wc.wcBlocksRegistry,
		l=window.wc.wcSettings,
		a=window.wp.i18n,
		i=window.wp.htmlEntities,
		s=(0,a.__)("Tumeny ","woo-tumeny"),
		r=({title:e})=>(0,i.decodeEntities)(e)||s,
		o=({description:e})=>(0,i.decodeEntities)(e||""),
		n=({logoUrls:t,label:l})=>(0,e.createElement)("div",{style:{display:"flex",flexDirection:"row",gap:"0.5rem",flexWrap:"wrap"}},
			t.map(((t,a)=>(0,e.createElement)("img",{key:a,src:t,alt:l})))),c=(0,l.getSetting)("tumeny_data",{}),
		d=r({title:c.title}),w={
			name:"tumeny",
			label:(0,e.createElement)((({logoUrls:t,title:l})=>(0,e.createElement)(e.Fragment,null,(0,e.createElement)("div",{style:{display:"flex",flexDirection:"row",gap:"0.5rem"}},(0,e.createElement)("div",null,r({title:l})),(0,e.createElement)(n,{logoUrls:t,label:r({title:l})})))),{logoUrls:c.logo_urls,title:d}),content:(0,e.createElement)(o,{description:c.description}),edit:(0,e.createElement)(o,{description:c.description}),canMakePayment:()=>!0,ariaLabel:d,supports:{showSavedCards:c.allow_saved_cards,showSaveOption:c.allow_saved_cards,features:c.supports}};(0,t.registerPaymentMethod)(w)})();