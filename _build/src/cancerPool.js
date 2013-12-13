// cancerPool.js (癌別分類表)
var cancerTaxonomy = (function () {
	var category = ['（請選擇）', '頭頸部', '消化系統', '呼吸器官', '造血系統', '骨骼', '軟組織', '皮膚', '乳房', '生殖器官', '泌尿系統', '眼睛', '腦脊髓部', '內分泌淋巴腺', '未知'],
		cancer = [
			[ // 預設空白類(請選擇)
				['', '　']
			], [ // 1:頭頸部
				['140', 'Lip 唇'],
				// ['140.3', 'Upper lip 上唇'],
				// ['140.4', 'Lower lip 下唇'],
				['141', 'Tongue 舌'],
				['141.0', 'Basal tongue 舌根'],
				['142', 'Salivary gland 主要唾液腺'],
				['142.0', 'Parotid gland 腮腺'],
				['142.1', 'Submandibular gland 頜下腺'],
				['142.2', 'Sublingual gland 舌下腺'],
				['143', 'Gum(gingiva) 牙齦'],
				// ['143.0', 'Upper gum 上牙齦'],
				// ['143.1', 'Lower lip 下牙齦'],
				['144', 'Floor of mouth 口底'],
				['145', 'Buccal 口腔'],
				['145.0', 'Buccal mucosa 頰粘膜'],
				['145.2', 'Hard palate 硬顎'],
				['145.3', 'Soft palate 軟顎'],
				['145.6', 'Retromolar trigone 後臼齒區'],
				['146', 'Oropharynx 口咽'],
				['146.0', 'Tonsil 扁桃腺'],
				['147', 'Nasopharynx 鼻咽'],
				['148', 'Hypopharynx 下咽'],
				['148.1', 'Pyrigorm sinus 下咽梨狀竇']
			], [ // 2:消化系統
				['150', 'Esophagus 食道'],
				['150.3', 'Esophagus upper third 上１／３食道'],
				['150.4', 'Esophagus middle third 中１／３食道'],
				['150.5', 'Esophagus lower third 下１／３食道'],
				['151', 'Stomach 胃'],
				['152', 'Duodenum and small intestine 十二指腸和小腸'],
				['153', 'Colon 結腸'],
				['153.2', 'Descending(Left) colon 降（左）結腸'],
				['153.3', 'Sigmoid colon 乙狀結腸'],
				['154', 'Anal rectum 肛門直腸'],
				['154.0', 'Rectosigmoid 直腸乙狀結腸連結處'],
				['154.1', 'Rectum 直腸'],
				['154.2', 'Anal canal，Sphincter 肛管括約肌'],
				['155', 'Liver 肝'],
				['156', 'Gallbladder and bile duct 膽囊和膽管'],
				['157', 'Pancreas 胰腺'],
				['158', 'Retroperitoneum organs 腹膜後器官']
			], [ // 3呼吸器官
				['160', 'Nasal cavity 鼻腔'],
				['161', 'Larynx 喉'],
				['161.0', 'Glottis, vocal cord 聲門'],
				['161.1', 'Supraglottis 上聲門'],
				['161.2', 'Subglottis 下聲門'],
				['162', 'Trachea, bronchus, and lung 氣管，支氣管和肺'],
				['164', 'Thymus, heart, and mediastinum 胸腺，心臟和縱隔'],
				['165', 'Paranasal sinus 鼻竇'],
				['165.0', 'Maxillary sinus 上頜竇'],
				['165.1', 'Ethmoid sinus 篩竇'],
				['167', 'Extemal ear, canal 外耳管'],
				['167.1', 'Middle, inner ear and Eustachian tube 中內耳耳咽管']
			], [ // 4造血系統
				['169', 'Hematopoietic and reticuloendothelial system 造血系統和網狀內皮系統'],
				['169.0', 'Blood(eg. Leukemia) 白血病'],
				['169.1', 'Bone marrow (eg. Multiple myeloma, Plasmacytoma) 骨髓（如多發性骨髓瘤，漿細胞骨髓）']
			], [ // 5骨骼
				['170', 'Bones, joints and articular cartilage 骨骼，關節和關節軟骨'],
				['170.2', 'Vertebra 脊椎'],
				['170.8', 'Tibia, fibula, and foot bone 脛骨，腓骨和足部骨']
			], [ // 6軟組織
				['171', 'Connective, subcutaneous and other soft tissue 結締組織，皮下和其他軟組織'],
				['171.0', 'Head and neck(exclude eye) 頭部和頸部（不包括眼睛）'],
				['171.1', 'Extremities 四肢'],
				['171.2', 'Trunk (include chest & chest &abdwall，retroperitoneum) 軀幹']
			], [ // 7皮膚
				['173', 'Skin 皮膚'],
				['173.3', 'Skin of face and neck 臉部和頸部的皮膚'],
				['173.5', 'Skin of extremities 四肢皮膚']
			], [ // 8乳房
				['174', 'Female breast 乳房']
			], [ // 9生殖器官
				['180', 'Cervix uteri 子宮頸'],
				['182', 'Uterus 子宮'],
				['183', 'Ovary, fallopian tube 卵巢，輸卵管'],
				['184', 'Other female genital organs 其他女性生殖器官'],
				['184.0', 'Vagina 陰道'],
				['184.4', 'Vulva 外陰'],
				['185', 'Prostate and seminal vesicle 前列腺和精囊'],
				['186', 'Testis 睾丸'],
				['187', 'Penis, scrotum and other male genital organs 陰莖，陰囊及其他男性生殖器官']
			], [ // 10泌尿系統
				['188', 'Urinary bladder 膀胱'],
				['189', 'Kidney and other urinary organs 腎臟等泌尿器官'],
				['189.0', 'Kidney 腎'],
				['189.1', 'Renal pelvis 腎盂']
			], [ // 11眼睛
				['190', 'Eye and lacrimal gland 眼睛和淚腺'],
				['190.1', 'Retrobulbar and orbital content 球後及眶內容物'],
				['190.5', 'Retina 視網膜']
			], [ // 12腦脊髓部
				['191', 'Brain 腦'],
				['191.0', 'Supratentorial(frontal, parietal, temporal, occipital lobe) 幕上（額葉，頂葉，顳葉，枕葉）'],
				['191.1', 'Infratentorial(cerebellar hemisphere, vermis) 幕下（小腦半球，小腦蚓部）'],
				['191.2', 'Brain stem(midbrain, thalamus, basal ganglia) 腦幹（中腦，丘腦，基底節）'],
				['191.3', 'Pineal gland 松果腺體'],
				['191.4', 'Suprasellar area 蝶鞍區'],
				['192', 'Other nervous system 其他神經系統'],
				['192.0', 'Cranial nerve(C-P angle tumor and optic glioma) 顱神經（C-P角腫瘤，視神經膠質瘤）'],
				['192.1', 'Cerebral meninges 腦膜'],
				['192.2', 'Spinal cord 脊髓']
			], [ // 13內分泌淋巴腺
				['193', 'Thyroid gland 甲狀腺'],
				['194', 'Other endocrine glands 其他內分泌腺'],
				['194.3', 'Pituitary 腦下垂體'],
				['195', 'Hodgkin\'s disease 霍奇金病'],
				['196', 'Non-hodgkin\'s lymphoma 非霍奇金淋巴瘤'],
				['196.0', 'CNS lymphoma 中樞神經系統淋巴瘤']
			], [ // 14未知
				['199', 'Unknown primary site 原發部位不明'],
				['199.0', 'Unknown primary site of head and neck origin 原發部位不明的頭部和頸部起源'],
				['199.1', 'Unknown primary site with distant metastasis 有遠處轉移的原發部位不明']
			]
		];

	return {
		categoryList: function () {
			return category;
		},
		cancerList: function (categoryIdx) { // 某大類下的cancer
			return cancer[categoryIdx];
		},
		categoryIdx: function (code) { // 依cancer code找出屬於哪一大類(idx)
			if (code >= '140' && code < '150') {
				return 1;
			} else if (code >= '150' && code < '160') {
				return 2;
			} else if (code >= '160' && code < '169') {
				return 3;
			} else if (code >= '169' && code < '170') {
				return 4;
			} else if (code >= '170' && code < '171') {
				return 5;
			} else if (code >= '171' && code < '173') {
				return 6;
			} else if (code >= '173' && code < '174') {
				return 7;
			} else if (code >= '174' && code < '180') {
				return 8;
			} else if (code >= '180' && code < '188') {
				return 9;
			} else if (code >= '188' && code < '190') {
				return 10;
			} else if (code >= '190' && code < '191') {
				return 11;
			} else if (code >= '191' && code < '193') {
				return 12;
			} else if (code >= '193' && code < '199') {
				return 13;
			} else if (code >= '199' && code < '200') {
				return 14;
			} else {
				return null;
			}
		},
		findCancer: function (code) {
			var categoryIdx = this.categoryIdx(code),
				pool = cancer[categoryIdx],
				i;
			for (i = 0; i < pool.length; i++) {
				if (pool[i][0] == code) {
					return {category: categoryIdx, cancer: pool[i]};
				}
			}
			return null;
		}
	};
})();