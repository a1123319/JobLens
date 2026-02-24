package com.joblens.joblens.entity;

import jakarta.persistence.Entity;
import jakarta.persistence.Id;
import jakarta.persistence.OneToOne;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.Table;

@Entity
@Table(name = "recruitmentsource")
public class RecruitmentSource {

    @Id
    private Integer companyId;

    @OneToOne
    @JoinColumn(name = "CompanyId", referencedColumnName = "Id", insertable = false, updatable = false)
    private Company company;

    private String oneHundredAndFour;
    private String official;

    // Getters and setters
    public Integer getCompanyId() {
        return companyId;
    }

    public void setCompanyId(Integer companyId) {
        this.companyId = companyId;
    }

    public Company getCompany() {
        return company;
    }

    public void setCompany(Company company) {
        this.company = company;
    }

    public String getOneHundredAndFour() {
        return oneHundredAndFour;
    }

    public void setOneHundredAndFour(String oneHundredAndFour) {
        this.oneHundredAndFour = oneHundredAndFour;
    }

    public String getOfficial() {
        return official;
    }

    public void setOfficial(String official) {
        this.official = official;
    }
}
