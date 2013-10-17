
var commonOptions=['一點也不','有一點','有些','相當','非常'], // 通用選項
	// 題庫
	quizPool={
		G1:{quiz:'工作雇用狀態',options:['全職','兼職','家庭主婦','失業','退休','殘障不能就業']},
		G2:{quiz:'婚姻狀態',options:['已婚或同居','未婚','分居','離婚','喪偶']},
		G3:{quiz:'生活居住情形',options:['獨居','與成人一同居住(無兒童)','與其他成人與兒童共同居住','只與兒童一同居住']},

		GP1:{quiz:'我精神不好',foreword:'以下是那些跟您有同樣疾病的人所認為重要的一些陳述，請在每一行陳述之後圈選出一個數字，以表達您認為在過去七天來各項陳述的真實程度'},
		GP2:{quiz:'我有噁心反胃的情形'},
		GP3:{quiz:'因為我的身體狀況，我有困難達到家人的需求'},
		GP4:{quiz:'我有疼痛'},
		GP5:{quiz:'我對治療的副作用感到困擾'},
		GP6:{quiz:'我覺得身體不適'},
		GP7:{quiz:'我因病被迫要臥床休息'},

		GS1:{quiz:'我覺得與我的朋友親近'},
		GS2:{quiz:'我從我家人獲得情緒上的支持'},
		GS3:{quiz:'我從我朋友獲得支持'},
		GS4:{quiz:'我家人已接受我的疾病'},
		GS5:{quiz:'我滿意家人之間對我疾病的溝通方式'},
		GS6:{quiz:'我覺得與我的伴侶(或我主要支持者)親近'},
		GS7:{quiz:'我對我的性生活感到滿意',options:['一點也不','有一點','有些','相當','非常','不想回答']},

		GE1:{quiz:'我感到悲傷'},
		GE2:{quiz:'我滿意自己處理疾病的方式'},
		GE3:{quiz:'我逐漸失去對抗我的疾病的希望'},
		GE4:{quiz:'我覺得緊張'},
		GE5:{quiz:'我擔心死亡'},
		GE6:{quiz:'我擔心我的狀況會惡化'},

		GF1:{quiz:'我能夠工作(包括在家的工作)'},
		GF2:{quiz:'我的工作(包括在家的工作)令人滿意'},
		GF3:{quiz:'我能夠享受生活'},
		GF4:{quiz:'我已接受我的疾病'},
		GF5:{quiz:'我睡得好'},
		GF6:{quiz:'我依然享受我以前常做的有趣的事'},
		GF7:{quiz:'我滿足我現在的生活品質'},

		E1:{quiz:'我最近三個月的平均收入每個月約新台幣',options:['無收入','0-10,000元','10,001-30,000元','30,001-50,000元','50,001-100,000元','100,000-200,000元','大於200,000元']},
		E2:{quiz:'我最近三個月平均每日工作時數約',options:['無','0-2.0小時','2.1-4.0小時','4.1-8.0小時','8.1-10.0小時','大於10.1小時']},
		E3:{quiz:'我最近三個月可支配自由使用的錢約有',options:['無收入','0-10,000元','10,001-30,000元','30,001-50,000元','50,001-100,000元','100,000-200,000元','大於200,000元']},

		B1:{quiz:'我呼吸時曾有氣不足'},
		B2:{quiz:'我在意自己的衣服穿著'},
		B3:{quiz:'我有一側或兩側的手臂腫脹或疼痛'},
		B4:{quiz:'我覺得自己是性感的'},
		B5:{quiz:'我對失去頭髮感到困擾'},
		B6:{quiz:'我擔心其他家人也會有得跟我同樣疾病的風險'},
		B7:{quiz:'我擔心壓力會影響到我的疾病'},
		B8:{quiz:'我對體重的改變感到困擾'},
		B9:{quiz:'我能夠覺得自己像個女人'},

		P2:{quiz:'我感到身體的某些部位有疼痛的症狀'},

		_PART_OF_PAIN:{quiz:'疼痛的部位',options:['沒有疼痛','頭頸部','身體','身體加頭頸部']},
		_SCORE_OF_PAIN:{quiz:'疼痛十分量表',options:[1,2,3,4,5,6,7,8,9,10]},

		_HN1:{quiz:"我覺得疲勞嗎?",options:["不會覺得","輕微的疲勞","中等程度的疲勞,讓我較不方便去做每天必須做的正常活動(讓我日常活動有受些影響)","非常疲勞,讓我無法執行每天必須的正常活動(讓我日常活動嚴重受影響)"]},
		_HN2:{quiz:"我會失眠嗎?",options:["不會","偶爾會失眠","中等程度失眠,使我的能力表現受到影響","嚴重失眠,已經使我無法執行每天必須的正常活動(讓我日常生活嚴重受影響)"]},
		_HN3:{quiz:"我有額外補充營養嗎?",options:["沒有","偶爾很少","有額外補充口服營養食品","有用管灌來補充營養,或用靜脈注射營養來補充"]},
		_HN4:{quiz:"我感到疼痛嗎?",options:["不會感到疼痛","輕微疼痛,不需止痛藥","已需要靠止痛藥來緩解疼痛","中等程度疼痛,需要止痛藥,使我日常生活稍微受到影響"]},
		_HN5:{quiz:"我的右耳聽力有變差嗎?",options:["聽力沒有受影響","聽力稍差","聽力變差,妨礙到我每天的正常活動","已需要助聽器"]},
		_HN6:{quiz:"我的左耳聽力有變差嗎?",options:["聽力沒有受影響","聽力稍差","聽力變差,妨礙到我每天的正常活動","已需要助聽器"]},
		_HN7:{quiz:"我右耳會耳鳴嗎?",options:["沒有耳鳴","輕微耳鳴","有時會,有時不會,不定時會發作","嚴重耳鳴,妨礙到我每天的正常活動"]},
		_HN8:{quiz:"我左耳會耳鳴嗎?",options:["沒有耳鳴","輕微耳鳴","有時會,有時不會,不定時會發作","嚴重耳鳴,妨礙到我每天的正常活動"]},
		_HN9:{quiz:"我右眼會感到視力模糊嗎?",options:["不會","稍微模糊,沒什麼影響","中等模糊,使我的功能受到影響","非常模糊,妨礙到我每天的正常活動"]},
		_HN10:{quiz:"我左眼會感到視力模糊嗎?",options:["不會","稍微模糊,沒什麼影響","中等模糊,使我的功能受到影響","非常模糊,妨礙到我每天的正常活動"]},
		_HN11:{quiz:"我會有厭食的感覺嗎?",options:["沒有","食慾稍差,但進食的量與質並沒有受到影響","食慾中等變差,需要額外口服的營養來補充","食慾很差,體重因此減輕,需要管灌來補充營養"]},
		_HN12:{quiz:"我會有噁心反胃的感覺嗎?",options:["沒有","稍有噁心反胃的感覺,但進食的量與質並沒有受到影響","有噁心反胃的感覺,進食量減少,需要額外由口來補充營養","強烈的噁心反胃的感覺,沒什麼進食,體重因此減輕,需要管灌來補充"]},
		_HN13:{quiz:"我有發生嘔吐的情形嗎?",options:["沒有","ㄧ天之內發生1次","ㄧ天之內發生2-5次","ㄧ天之內發生6次以上"]},
		_HN14:{quiz:"我會有口腔黏膜發炎疼痛或喉嚨吞嚥痛嗎?",options:["不會","輕微疼痛,但進食仍維持正常","中等疼痛,只能吃半流質或軟性的食物","嚴重疼痛,只能喝流質食物"]},
		_HN15:{quiz:"我的吞嚥功能正常嗎?",options:["正常","有點不順,但仍能正常進食","中等程度不順,進食量減少,需要額外由口來補充營養","非常不順,感到難以吞嚥,需要管灌來補充營養"]},
		_HN16:{quiz:"我的牙齒狀況?",options:["沒有任何蛀牙及牙疼或裝假牙","有蛀牙或牙疼,但不需拔牙","有蛀牙或牙疼,牙齒碎裂,需要治療","因治療的副作用全口牙都拔除了"]},
		_HN17:{quiz:"我會感到口乾嗎?",options:["不會","口水變的有點乾黏,但不影響正常進食","口水分泌量減少,所以吃飯需要配水才能吞嚥下去","嚴重的口乾,無法進食,需要管灌來補充營養"]},
		_HN18:{quiz:"我說話的聲音有變沙啞嗎?",options:["沒有改變","輕微或偶而沙啞,別人可以聽懂我說的話","持續的沙啞,但仍可以用電話溝通,說話需要重複,別人還是可以聽懂","較為沙啞,只能面對面與別人溝通,偶而需要輔助器"]},
		_HN19:{quiz:"我的鼻子狀況為何?",options:["沒有不舒服","容易有結痂或血絲","鼻黏膜水腫,稍微不暢通","嚴重鼻塞,妨礙到我每天的正常活動"]},
		_HN20:{quiz:"我的牙關活動能力為何?",options:["嘴巴活動自如","活動範圍稍受限制,但不影響正常進食","吃東西時,我只能咬小小口而已","嘴巴張不太開,只能喝流質食物"]},
		_HN21:{quiz:"我會感到頭暈嗎?",options:["不會","輕微頭暈,但不影響我的正常功能","中等頭暈,會使我的能力表現變差","嚴重頭暈,妨礙到我每天必須的正常活動"]},
		_HN22:{quiz:"我的記憶力有受影響嗎?",options:["沒有","輕微減退,但不影響我的正常功能","中等減退,會使我的能力表現變差","嚴重減退,妨礙到我每天必須的正常活動"]},

		"H&N1":{quiz:"我能夠吃我愛吃的食"},
		"H&N2":{quiz:"我覺得口乾"},
		"H&N3":{quiz:"我呼吸有困難"},
		"H&N4":{quiz:"我聲音的音質和音量跟平常一樣"},
		"H&N5":{quiz:"我能夠吃我想吃的份量"},
		"H&N6":{quiz:"我對自己臉部及頸部的樣子感到不快"},
		"H&N7":{quiz:"我能吞嚥自如"},
		"H&N8":{quiz:"我吸香菸或其它煙草產品"},
		"H&N9":{quiz:"我喝酒(例如：啤酒、葡萄酒等等)"},
		"H&NA":{quiz:"我能夠與其他人溝通"},
		"H&NB":{quiz:"我能夠吃固體食物"},

		X1:{quiz:"我的嘴巴或舌頭很乾"},
		X2:{quiz:"整體而言，我的嘴巴或舌頭很舒服"},
		X3:{quiz:"由於口乾，我不能好好入睡"},
		X4:{quiz:"由於口乾，我說話有困難"},
		X5:{quiz:"由於口乾，我不易進食"},
		X6:{quiz:"您是否使用活動式假牙",options:["是","否"]},
		X7:{quiz:"我因為口腔不適而影響使用假牙"},
		_X8:{quiz:"我的嘴巴，喉嚨或脖子會痛"},
		_X9:{quiz:"我的肩及頸因僵硬而活動困難"},
		_X10:{quiz:"我對耳鳴感到困擾"},
		_X11:{quiz:"我的聽力有問題"},
		_X12:{quiz:"我對視力轉壞感到困擾"},
		_X13:{quiz:"我的嗅覺有問題"},
		_X14:{quiz:"我能享受食物的味道"},
		_X15:{quiz:"我對鼻塞感到困擾"},

		_BLANK:{quiz:''}
	},
	// 引言template(for後台)
	foreword_view=_.template('<hr/><small class="text-danger"><%= foreword %></small>'),
	// 問題template(for後台)
	quiz_view=_.template(
			'<hr/><h4><%= idx %>. <%= quiz %></h4>'+
			'<div class="col-lg-12">'+
			'<%_.forEach(options, function (o,i) {%>'+
			'	<label class="checkbox-inline"><input type="radio" name="<%= name %>" value="<%= i %>"/> <%= o %></label>'+
			'<%});%>'+
			'<label class="checkbox-inline"><input type="radio" name="<%= name %>" value="-1"/> 不想回答</label>'+
			'</div>'
		);
