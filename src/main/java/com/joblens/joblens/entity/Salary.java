package com.joblens.joblens.entity;

import jakarta.persistence.Entity;
import jakarta.persistence.Id;
import jakarta.persistence.OneToOne;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.Table;

@Entity
@Table(name = "salary")
public class Salary {

    @Id
    private Integer companyId;

    @OneToOne
    @JoinColumn(name = "CompanyId", referencedColumnName = "Id", insertable = false, updatable = false)
    private Company company;

    private Integer nonAdminstrativeAverage;
    private Integer nonAdminstrativeMedian;
    private Integer average;

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

    public Integer getNonAdminstrativeAverage() {
        return nonAdminstrativeAverage;
    }

    public void setNonAdminstrativeAverage(Integer nonAdminstrativeAverage) {
        this.nonAdminstrativeAverage = nonAdminstrativeAverage;
    }

    public Integer getNonAdminstrativeMedian() {
        return nonAdminstrativeMedian;
    }

    public void setNonAdminstrativeMedian(Integer nonAdminstrativeMedian) {
        this.nonAdminstrativeMedian = nonAdminstrativeMedian;
    }

    public Integer getAverage() {
        return average;
    }

    public void setAverage(Integer average) {
        this.average = average;
    }
}
